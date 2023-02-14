<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\Settings\Form;

use Psr\Http\Message\ServerRequestInterface;
use srag\Plugins\H5P\Settings\ISettingsRepository;
use srag\Plugins\H5P\Form\AbstractFormProcessor;
use ILIAS\UI\Component\Input\Container\Form\Form as UIForm;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class ObjectSettingsFormProcessor extends AbstractFormProcessor
{
    /**
     * @var ISettingsRepository
     */
    protected $repository;

    /**
     * @var \ilObjH5P
     */
    protected $object;

    public function __construct(
        ServerRequestInterface $request,
        UIForm $form,
        ISettingsRepository $repository,
        \ilObjH5P $object
    ) {
        parent::__construct($request, $form);
        $this->repository = $repository;
        $this->object = $object;
    }

    /**
     * @inheritDoc
     */
    protected function isValid(array $post_data): bool
    {
        // form data does not require further validation.
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function processData(array $post_data): void
    {
        $object_settings = $this->repository->getObjectSettings($this->object->getId());

        if (null === $object_settings) {
            $object_settings = new \ilH5PObjectSettings();
            $object_settings->setObjId($this->object->getId());
        }

        $this->object->setTitle($post_data[ObjectSettingsFormBuilder::INPUT_TITLE]);
        $this->object->setDescription($post_data[ObjectSettingsFormBuilder::INPUT_DESCRIPTION]);
        $this->object->update();

        $object_settings->setOnline($post_data[ObjectSettingsFormBuilder::INPUT_ONLINE]);
        $object_settings->setSolveOnlyOnce($post_data[ObjectSettingsFormBuilder::INPUT_SOLVE_ONCE]);

        $this->repository->storeObjectSettings($object_settings);
    }
}
