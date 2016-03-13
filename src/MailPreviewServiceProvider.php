<?php

namespace Themsaid\MailPreview;

use Swift_Mailer;
use Illuminate\Mail\MailServiceProvider;

class MailPreviewServiceProvider extends MailServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/mailpreview.php' => config_path('mailpreview.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->mergeConfigFrom(
            __DIR__.'/config/mailpreview.php', 'mailpreview'
        );
    }

    /**
     * Register the Swift Mailer instance.
     *
     * @return void
     */
    function registerSwiftMailer()
    {
        if ($this->app['config']['mail.driver'] == 'preview') {
            $this->registerPreviewSwiftMailer();
        } else {
            parent::registerSwiftMailer();
        }
    }

    /**
     * Register the Preview Swift Mailer instance.
     *
     * @return void
     */
    private function registerPreviewSwiftMailer()
    {
        $this->app['swift.mailer'] = $this->app->share(function ($app) {
            return new Swift_Mailer(
                new PreviewTransport(
                    $app->make('Illuminate\Filesystem\Filesystem'),
                    $app['config']['mailpreview.path'],
                    $app['config']['mailpreview.maximum_lifetime']
                )
            );
        });
    }
}