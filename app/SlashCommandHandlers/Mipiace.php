<?php

namespace App\SlashCommandHandlers;

use App;
use Carbon\Carbon;
use Facebook\FacebookClient;
use Spatie\SlashCommand\Attachment;
use Spatie\SlashCommand\Handlers\BaseHandler;
use Spatie\SlashCommand\Request;
use Spatie\SlashCommand\Response;

/**
 * @package Mipiace
 * @author  Léopold Jacquot {@link https://www.leopoldjacquot.com}
 */
class Mipiace extends BaseHandler {
    const MI_PIACE_FACEBOOK_PAGE_ID = 896229970508805;

    /**
     * If this function returns true, the handle method will get called.
     *
     * @param \Spatie\SlashCommand\Request $request
     *
     * @return bool
     */
    public function canHandle(Request $request): bool {
        return true;
    }

    /**
     * Handle the given request. Remember that Slack expects a response
     * within three seconds after the slash command was issued. If
     * there is more time needed, dispatch a job.
     *
     * @param \Spatie\SlashCommand\Request $request
     *
     * @return \Spatie\SlashCommand\Response
     */
    public function handle(Request $request): Response {
        $facebook    = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
        $fbToken     = $this->getFacebookToken();
        $currentDate = Carbon::now();

        try {
            $response = $facebook->get('/' . self::MI_PIACE_FACEBOOK_PAGE_ID . '/posts', $fbToken);
            $edge     = $response->getGraphEdge();

            foreach ($edge->asArray() as $post) {
                if (!isset($post['message'])) {
                    continue;
                }

                if (strpos($post['message'], 'plats du jour') === false) {
                    continue;
                }

                if (strpos($post['message'], $currentDate->day) === false) {
                    continue;
                }

                if (strpos($post['message'], $currentDate->year) === false) {
                    continue;
                }

                $postResponse = $facebook->get($post['id'] . '?fields=object_id', $fbToken);
                $objectId     = $postResponse->getGraphNode()['object_id'];

                $attachement = Attachment::create();
                $attachement->setImageUrl(FacebookClient::BASE_GRAPH_URL . '/' . $objectId . '/picture');

                return $this->respondToSlack($post['message'])
                            ->withAttachment($attachement)
                            ->displayResponseToEveryoneOnChannel();
            }
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return $this->respondToSlack("Euuuh erreur erreur erreur!");
        }

        return $this->respondToSlack("Menu du jour pas encore publié!")
                    ->displayResponseToEveryoneOnChannel();
    }

    /**
     * @return string
     */
    private function getFacebookToken() {
        return env('FACEBOOK_APP_ID') . '|' . env('FACEBOOK_APP_SECRET');
    }
}