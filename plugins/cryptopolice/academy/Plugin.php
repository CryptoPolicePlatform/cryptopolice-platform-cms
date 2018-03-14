<?php namespace CryptoPolice\Academy;

use Carbon\Carbon;
use CryptoPolice\Academy\Models\FinalScore;
use System\Classes\PluginBase;
use CryptoPolice\Academy\Models\Settings;

class Plugin extends PluginBase
{

    public $require = [
        'RainLab.Location',
        'RainLab.Notify',
        'RainLab.User',
    ];

    public function registerComponents()
    {
        return [
            'CryptoPolice\Academy\Components\Exams'          => 'Exams',
            'CryptoPolice\Academy\Components\ExamTask'       => 'ExamTask',
            'CryptoPolice\Academy\Components\Recaptcha'      => 'reCaptcha',
            'CryptoPolice\Academy\Components\Trainings'      => 'Trainings',
            'CryptoPolice\Academy\Components\TrainingTask'   => 'TrainingTask',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'         => 'Platform Settings',
                'description'   => 'Settings',
                'icon'          => 'icon-cog',
                'class'         => 'CryptoPolice\Academy\Models\Settings',
            ]
        ];
    }

    public function boot()
    {

    }

    public function registerSchedule($schedule)
    {

        // 0**** php /path/to/file/artisan schedule:run >> /dev/null 2>&1

        $schedule->call(function () {

            $settings = Settings::instance();

            if ($settings->active) {

                $userScores = FinalScore::with('exam')
                    ->where('created_at', '>', Carbon::now()->subMinutes('10'))
                    ->where('created_at', '<', Carbon::now())
                    ->where('complete_status', 0)
                    ->get();

                $count = 0;

                foreach ($userScores as $score) {
                    $count++;
                    if (Carbon::now()->greaterThan(Carbon::parse($score->completed_at))) {
                        FinalScore::where('id', $score['id'])->update([
                            'complete_status' => 1
                        ]);
                    }
                }

                if ($settings->active_trace) {
                    trace_log("[exam_scores_cron] Query updated count:" . $count);
                }
            }

        })->everyMinute()
            ->name('final_scores')
            ->withoutOverlapping();
    }

}