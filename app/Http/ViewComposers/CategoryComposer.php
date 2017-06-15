<?php

namespace App\Http\ViewComposers;

use App\Repositories\Campaign\CampaignRepositoryInterface;
use App\Repositories\Contribution\ContributionRepositoryInterface;
use App\Repositories\Event\EventRepositoryInterface;
use App\Repositories\Follow\FollowRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Contracts\View\View;
use Request;

class CategoryComposer
{

    protected $campaignRepository;
    protected $contributionRepository;
    protected $eventRepository;
    protected $userRepository;
    protected $followRepository;

    public function __construct(
        CampaignRepositoryInterface $campaignRepository,
        ContributionRepositoryInterface $contributionRepository,
        EventRepositoryInterface $eventRepository,
        UserRepositoryInterface $userRepository,
        FollowRepositoryInterface $followRepository
    ) {
        $this->campaignRepository = $campaignRepository;
        $this->contributionRepository = $contributionRepository;
        $this->eventRepository = $eventRepository;
        $this->userRepository = $userRepository;
        $this->followRepository = $followRepository;
    }

    public function compose(View $view)
    {

        $id = Request::segment(config('constants.RATING_USER'));
        $url = Request::segment(config('constants.ACTIVATED'));

        if ($id && config('settings.campaigns') == $url
            || config('settings.confirmed') == $url
            || config('settings.unconfirmed') == $url
        ) {
            $this->dataView['events'] = $this->eventRepository->getEvent($id);
            $this->dataView['results'] = $this->contributionRepository->getValueContribution($id);
        } else {
            $this->dataView['events'] = [];
            $this->dataView['results'] = [];
        }

        if ($id && config('settings.follower') == $url
            || config('settings.following') == $url
            || config('settings.user') == $url
        ) {
            $this->dataView['userTimeline'] = $this->userRepository->getTimeline($id);
            $this->dataView['following'] = $this->followRepository->following($id);
            $this->dataView['followers'] = $this->followRepository->followers($id);
        }

        $this->dataView['campaign'] = $this->campaignRepository->lastCampaign();

        $view->with($this->dataView);
    }
}
