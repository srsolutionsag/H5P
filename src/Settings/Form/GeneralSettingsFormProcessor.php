<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Settings\Form;

use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Form\AbstractFormProcessor;
use Psr\Http\Message\ServerRequestInterface;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class GeneralSettingsFormProcessor extends AbstractFormProcessor
{
    /**
     * @var ISettingsRepository
     */
    protected $repository;

    public function __construct(ServerRequestInterface $request, UIForm $form, ISettingsRepository $repository)
    {
        parent::__construct($request, $form);
        $this->repository = $repository;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        // data doesn't require additional validation.
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        foreach ($post_data as $name => $value) {
            $this->repository->storeGeneralSetting($name, $value);
        }
    }
}
