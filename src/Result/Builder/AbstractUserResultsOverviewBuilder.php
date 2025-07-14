<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Result\Builder;

use srag\Plugins\H5P\Result\IResult;
use srag\Plugins\H5P\DateTimeConversion;
use srag\Plugins\H5P\IRequestParameters;
use srag\Plugins\H5P\IGeneralRepository;
use srag\Plugins\H5P\ITranslator;
use ILIAS\UI\Implementation\Component\ComponentHelper;
use ILIAS\UI\Component\Button\Button;
use ILIAS\UI\Renderer as ComponentRenderer;
use ILIAS\UI\Factory as ComponentFactory;
use DateTimeZone;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
abstract class AbstractUserResultsOverviewBuilder
{
    use \ilH5PDisplayNameHelper;
    use \ilH5PTargetHelper;
    use DateTimeConversion;
    use ComponentHelper;

    /**
     * @var IGeneralRepository
     */
    protected $general_repository;

    /**
     * @var ComponentFactory
     */
    protected $components;

    /**
     * @var ComponentRenderer
     */
    protected $renderer;

    /**
     * @var DateTimeZone
     */
    protected $user_time_zone;

    /**
     * @var ITranslator
     */
    protected $translator;

    public function __construct(
        IGeneralRepository $general_repository,
        ComponentFactory $components,
        ComponentRenderer $renderer,
        DateTimeZone $user_time_zone,
        ITranslator $translator,
        \ilCtrl $ctrl
    ) {
        $this->general_repository = $general_repository;
        $this->components = $components;
        $this->renderer = $renderer;
        $this->user_time_zone = $user_time_zone;
        $this->translator = $translator;
        $this->ctrl = $ctrl;
    }

    protected function getDeleteUserResultsButton(ComponentFactory $components, IResult $result): Button
    {
        return $components->button()->standard(
            $this->translator->txt('delete'),
            $this->getLinkTarget(\ilH5PResultGUI::class, \ilH5PResultGUI::CMD_CONFIRM_DELETE_SINGLE_USER_RESULTS, [
                IRequestParameters::CONTENT_ID => $result->getContentId(),
                IRequestParameters::USER_ID => $result->getUserId(),
            ])
        );
    }

    /**
     * Returns the duration of a result as "hh:mm:ss" or e.g. "00:01:23".
     */
    protected function getFormattedResultDuration(IResult $result): string
    {
        return $this->getFormattedDuration($result->getTime());
    }

    /**
     * Returns the formatted score of a result as "x / y" or e.g. "1 / 3".
     */
    protected function getFormattedResultScore(IResult $result): string
    {
        return "{$result->getScore()} / {$result->getMaxScore()}";
    }

    protected function getDisplayDateTimeZone(): DateTimeZone
    {
        return $this->user_time_zone;
    }

    protected function getCtrl(): \ilCtrl
    {
        return $this->ctrl;
    }

    protected function getTranslator(): ITranslator
    {
        return $this->translator;
    }
}
