<?php namespace CryptoPolice\Academy;

use Carbon\Carbon;
use CryptoPolice\Academy\Models\FinalScore;
use System\Classes\PluginBase;

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
        // ***** php /path/to/file/artisan schedule:run >> /dev/null 2>&1

        trace_log('cron_final_score');
        $schedule->call(function () {

            $userScores = FinalScore::with('exam')
                ->where('created_at', '>', Carbon::now()->subMinutes('10'))
                ->where('created_at', '<', Carbon::now())
                ->where('complete_status', 0)
                ->get();

            foreach ($userScores as $score) {
                if (Carbon::now()->greaterThan(Carbon::parse($score->completed_at))) {

                    FinalScore::where('id', $score['id'])->update([
                        'complete_status' => 1
                    ]);
                }
            }

        })->everyMinute()
            ->name('final_scores')
            ->withoutOverlapping();
    }

}