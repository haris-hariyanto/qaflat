<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Import;
use Illuminate\Support\Facades\Http;
use PhpZip\ZipFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Content;
use App\Models\Subject;
use App\Models\Grade;
use App\Models\MetaData;

class ImportContentsBulk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:import:save:bulk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Import Contents');

        $totalFileToImport = $this->ask('Jumlah file untuk diimport', 1);
        for ($i = 1; $i <= $totalFileToImport; $i++) {
            $filesImported = Import::get()->pluck('identifier')->toArray();

            $getFiles = Http::get('https://enchanting-buttercream-108d13.netlify.app/qa-db-ybzdgjrtmt.json');
            $files = json_decode($getFiles->body(), true);

            $filesToImport = [];
            $filesURLs = [];
            foreach ($files as $file) {
                if (!in_array($file['url'], $filesImported)) {
                    $code = $file['fileName'] . ' (' . $file['url'] . ')';
                    $filesToImport[] = $code;
                    $filesURLs[$code] = $file['url'];
                }
            }

            if (count($filesToImport) == 0) {
                $this->line('[ * ] Semua file sudah diimport');
                return false;
            }

            $fileToImport = $filesToImport[0];

            $fileURL = $filesURLs[$fileToImport];

            $this->line('[ * ] Mendownload file ' . $fileToImport);
            $this->getPackedFile($fileURL);

            Import::firstOrCreate([
                'identifier' => $fileURL,
            ]);

            $this->saveAll();
        } // [END] for
    }

    public function getPackedFile($fileURL)
    {
        $saveDestination = 'packed-files/packed-files.zip';

        Storage::disk('local')->put($saveDestination, file_get_contents($fileURL));

        $this->line('[ * ] File telah didownload');
        $this->line('[ * ] Mengekstrak file');

        $zipFile = new ZipFile();
        $zipFile
            ->openFile(storage_path('app/' . $saveDestination))
            ->extractTo(storage_path('app/packed-files'));
        $this->line('[ * ] File telah diekstrak');

        Storage::disk('local')->delete($saveDestination);
    }

    public function saveAll()
    {
        $this->line('Save Contents');

        $files = Storage::disk('local')->files('packed-files');
        $filesTotal = count($files);

        $this->line('[ * ] Total file yang belum disimpan ke database : ' . $filesTotal);
        $this->line('[ * ] Masukkan jumlah file untuk disimpan');
        $this->line('[ * ] Masukkan * untuk menyimpan semua file');

        $loop = true;
        while ($loop) {
            $limitFiles = '*';

            if (is_numeric($limitFiles) || $limitFiles === '*') {
                $loop = false;
            }
        }

        if ($limitFiles === '*') {
            $limitFiles = $filesTotal;
        }

        $currentContentIDModel = MetaData::where('key', 'current_content_id')->first();
        $currentContentID = $currentContentIDModel->value;

        $currentLoop = 1;
        $lastQuestion = '';
        foreach ($files as $file) {
            $this->line('[ ' . $currentLoop . ' ] Mengimport ' . $file);

            $content = Storage::disk('local')->get($file);
            $content = json_decode($content, true);

            if (!$content || count($content['answers']) == 0 || $lastQuestion == $content['question']) {
                $this->error('[ * ] Skip');

                Storage::disk('local')->delete($file);
                $currentLoop++;

                continue;
            }

            $slug = preg_replace('/[^A-Za-z0-9 ]/', '', $content['question']);
            $slug = Str::words($slug, '7', '');
            if (strlen($slug) > 70) {
                $slug = substr($slug, 0, 70);
            }
            $slug = $currentContentID . '-' . Str::slug($slug);
            $currentContentID++;

            $answersToSave = [];
            $count = 1;
            $answers = $content['answers'];
            $previousAnswer = false;
            foreach ($answers as $answer) {
                if ($previousAnswer != $answer) {
                    $answersToSave[] = [
                        'answer' => html_entity_decode($answer),
                        'is_best' => $count == 1 ? 'Y' : 'N',
                        'vote' => $count == 1 ? rand(51, 100) : rand(1, 50),
                    ];
                }

                $previousAnswer = $answer;

                $count++;
                if ($count > 2) {
                    break;
                }
            }

            Content::create([
                'slug' => $slug,
                'question' => html_entity_decode($content['question']),
                'grade' => $content['grade'],
                'subject' => $content['subject'],
                'answers' => json_encode($answersToSave),
                'answers_total' => count($answersToSave),
            ]);

            Grade::firstOrCreate([
                'slug' => Str::slug($content['grade']),
                'name' => $content['grade'],
            ]);

            Subject::firstOrCreate([
                'slug' => Str::slug($content['subject']),
                'name' => $content['subject'],
            ]);

            Storage::disk('local')->delete($file);

            $currentLoop++;
            if ($currentLoop > $limitFiles) {
                break;
            }
        }

        $currentContentIDModel->update([
            'value' => $currentContentID,
        ]);

        // Create JSON data

        /** Single page */
        $contents = Content::orderBy('id', 'asc')->get();
        foreach ($contents as $content) {
            // Create related questions
            $relatedQuestions = Content::where('subject', $content->subject)
                ->where('grade', $content->grade)
                ->where('id', '<', $content->id)
                ->take(config('content.related_questions'))
                ->orderBy('id', 'desc')
                ->get();
            
            $relatedQuestionsCount = count($relatedQuestions);
            if ($relatedQuestionsCount < config('content.related_questions')) {
                $relatedQuestions = Content::where('subject', $content->subject)
                    ->where('grade', $content->grade)
                    ->where('id', '>', $content->id)
                    ->take(config('content.related_questions'))
                    ->orderBy('id', 'asc')
                    ->get();
            }

            $relatedQuestionsData = [];
            foreach ($relatedQuestions as $relatedQuestion) {
                $relatedQuestionsData[] = [
                    'question' => $relatedQuestion->question,
                    'answers' => json_decode($relatedQuestion->answers, true),
                ];
            }
            // [END] Create related questions

            // Create internal links
            $internalLinks = Content::where('id', '<', $content->id)
                ->take(config('content.related_questions'))
                ->orderBy('id', 'desc')
                ->get();

            $internalLinksCount = count($internalLinks);
            if ($internalLinksCount < config('content.related_questions')) {
                $internalLinks = Content::where('id', '>', $content->id)
                    ->take(config('content.related_questions'))
                    ->orderBy('id', 'asc')
                    ->get();
            }

            $internalLinksData = [];
            foreach ($internalLinks as $internalLink) {
                $internalLinksData[] = [
                    'slug' => $internalLink->slug,
                    'question' => $internalLink->question,
                ];
            }
            // [END] Create internal links

            $data = [];
            $data['slug'] = $content->slug;
            $data['question'] = $content->question;
            $data['grade'] = $content->grade;
            $data['subject'] = $content->subject;
            $data['answers'] = json_decode($content->answers, true);
            $data['related_questions'] = $relatedQuestionsData;
            $data['internal_links'] = $internalLinksData;
            $data = json_encode($data);

            $fileName = $content->slug . '.json';
            $this->line('[ * ] Membuat file ' . $fileName);
            Storage::put('single-pages/' . $fileName, $data);
        }

        /** Index page */
        $loop = true;
        $iteration = 1;
        while ($loop) {
            $skip = ($iteration - 1) * config('content.item_per_page');
            $contents = Content::orderBy('id', 'asc')
                ->skip($skip)
                ->take(config('content.item_per_page'))
                ->get();
            $contentsTotal = count($contents);
            
            if ($contentsTotal < config('content.item_per_page')) {
                $loop = false;
                break;
            }

            $data = [];
            foreach ($contents as $content) {
                $data[] = [
                    'slug' => $content->slug,
                    'question' => $content->question,
                    'grade' => $content->grade,
                    'subject' => $content->subject,
                    'answers_total' => $content->answers_total,
                ];
            }
            $data = json_encode($data);

            $currentIndexIteration = MetaData::where('key', 'current_index_iteration')->first();
            $currentIndexIterationValue = $currentIndexIteration->value;
            $currentIndexIterationValue++;

            $fileName = 'index-' . $currentIndexIterationValue . '.json';
            $this->line('[ * ] Membuat file ' . $fileName);
            Storage::put('index-pages/' . $fileName, $data);

            $currentIndexIteration->update([
                'value' => $currentIndexIterationValue,
            ]);

            $iteration++;
        }

        /** Grade page */
        $grades = Grade::get();
        foreach ($grades as $grade) {
            $loop = true;
            $iteration = 1;
            while ($loop) {
                $skip = ($iteration - 1) * config('content.item_per_page');
                $contents = Content::orderBy('id', 'asc')
                    ->where('grade', $grade->name)
                    ->skip($skip)
                    ->take(config('content.item_per_page'))
                    ->get();
                $contentsTotal = count($contents);

                if ($contentsTotal < config('content.item_per_page')) {
                    $loop = false;
                    break;
                }

                $data = [];
                foreach ($contents as $content) {
                    $data[] = [
                        'slug' => $content->slug,
                        'question' => $content->question,
                        'grade' => $content->grade,
                        'subject' => $content->subject,
                        'answers_total' => $content->answers_total,
                    ];
                }
                $data = json_encode($data);

                $gradeSlug = Str::slug($grade->name);

                $currentGradeIteration = MetaData::where('key', 'current_grade_iteration.' . $gradeSlug)->first();
                if (!$currentGradeIteration) {
                    $currentGradeIteration = MetaData::create([
                        'key' => 'current_grade_iteration.' . $gradeSlug,
                        'value' => 0,
                    ]);
                }
                $currentGradeIterationValue = $currentGradeIteration->value;
                $currentGradeIterationValue++;

                $fileName = 'grade-' . $gradeSlug . '-' . $currentGradeIterationValue . '.json';
                $this->line('[ * ] Membuat file ' . $fileName);
                Storage::put('grade-pages/' . $fileName, $data);

                $currentGradeIteration->update([
                    'value' => $currentGradeIterationValue,
                ]);

                $iteration++;
            }
        } // [END] foreach

        /** Subject page */
        $subjects = Subject::get();
        foreach ($subjects as $subject) {
            $loop = true;
            $iteration = 1;
            while ($loop) {
                $skip = ($iteration - 1) * config('content.item_per_page');
                $contents = Content::orderBy('id', 'asc')
                    ->where('subject', $subject->name)
                    ->skip($skip)
                    ->take(config('content.item_per_page'))
                    ->get();
                $contentsTotal = count($contents);

                if ($contentsTotal < config('content.item_per_page')) {
                    $loop = false;
                    break;
                }

                $data = [];
                foreach ($contents as $content) {
                    $data[] = [
                        'slug' => $content->slug,
                        'question' => $content->question,
                        'grade' => $content->grade,
                        'subject' => $content->subject,
                        'answers_total' => $content->answers_total,
                    ];
                }
                $data = json_encode($data);

                $subjectSlug = Str::slug($subject->name);

                $currentSubjectIteration = MetaData::where('key', 'current_subject_iteration.' . $subjectSlug)->first();
                if (!$currentSubjectIteration) {
                    $currentSubjectIteration = MetaData::create([
                        'key' => 'current_subject_iteration.' . $subjectSlug,
                        'value' => 0,
                    ]);
                }
                $currentSubjectIterationValue = $currentSubjectIteration->value;
                $currentSubjectIterationValue++;

                $fileName = 'subject-' . $subjectSlug . '-' . $currentSubjectIterationValue . '.json';
                $this->line('[ * ] Membuat file ' . $fileName);
                Storage::put('subject-pages/' . $fileName, $data);

                $currentSubjectIteration->update([
                    'value' => $currentSubjectIterationValue,
                ]);

                $iteration++;
            }
        } // [END] foreach

        /** Sitemap */
        $loop = true;
        $segment = 1;
        while ($loop) {
            $take = config('content.sitemap_items');
            $skip = $take * ($segment - 1);

            $contents = Content::select('slug')
                ->orderBy('id', 'asc')
                ->skip($skip)
                ->take($take)
                ->get();
            
            if (count($contents) < $take) {
                $loop = false;
            }

            $sitemap = [];
            foreach ($contents as $content) {
                $sitemap[] = [
                    'url' => route('content', [$content->slug]),
                    'created' => time(),
                ];
            }
            $sitemap = json_encode($sitemap);

            $currentSitemapIteration = MetaData::where('key', 'current_sitemap_iteration')->first();
            $currentSitemapIterationValue = $currentSitemapIteration->value;
            $currentSitemapIterationValue++;

            $fileName = 'sitemap-' . $currentSitemapIterationValue . '.json';
            $this->line('[ * ] Membuat sitemap ' . $fileName);
            Storage::put('sitemaps/' . $fileName, $sitemap);

            $currentSitemapIteration->update([
                'value' => $currentSitemapIterationValue,
            ]);

            $segment++;
        }

        Content::truncate();
        // [END] Create JSON data
    }
}
