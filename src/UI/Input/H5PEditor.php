<?php

declare(strict_types=1);

namespace srag\Plugins\H5P\UI\Input;

use srag\Plugins\H5P\Content\ContentEditorData;
use srag\Plugins\H5P\UI\Factory as H5PComponentFactory;
use ILIAS\UI\Implementation\Component\Input\Field\Group;
use ILIAS\UI\Implementation\Component\Input\InputData;
use ILIAS\UI\Component\Input\Field\Factory as FieldFactory;
use ILIAS\Refinery\Constraint;
use ILIAS\Refinery\Factory as Refinery;
use ILIAS\Data\Result\Ok;
use ILIAS\Data\Factory as DataFactory;
use ILIAS\UI\Component\Input\Field\Input;

/**
 * @author Thibeau Fuhrer <thibeau@sr.solutions>
 */
class H5PEditor extends Group
{
    protected const INPUT_EDITOR_ACTION = 'h5p_editor_action';
    protected const INPUT_CONTENT_LIBRARY = 'h5p_content_library';
    protected const INPUT_CONTENT_TITLE = 'h5p_content_title';
    protected const INPUT_CONTENT_JSON = 'h5p_content_json';
    protected const INPUT_CONTENT_ID = 'h5p_content_id';

    protected const EDITOR_ACTION_CREATE = 'create';

    /**
     * @inheritDoc
     */
    public function __construct(
        H5PComponentFactory $h5p_components,
        FieldFactory $field_factory,
        DataFactory $data_factory,
        Refinery $refinery,
        \ilLanguage $language,
        string $label,
        ?string $byline
    ) {
        parent::__construct($data_factory, $refinery, $language, [
            self::INPUT_CONTENT_LIBRARY => $h5p_components->hidden(),
            self::INPUT_CONTENT_TITLE => $h5p_components->hidden(),
            self::INPUT_CONTENT_JSON => $h5p_components->hidden(),
            self::INPUT_CONTENT_ID => $h5p_components->hidden(),
            self::INPUT_EDITOR_ACTION => $h5p_components->hidden()->withValue(self::EDITOR_ACTION_CREATE),
        ], $label, $byline);
    }

    /**
     * @inheritDoc
     */
    public function getValue(): ?ContentEditorData
    {
        $value = parent::getValue();

        if (empty($value)) {
            return null;
        }

        return new ContentEditorData(
            $value[self::INPUT_CONTENT_ID],
            $value[self::INPUT_CONTENT_TITLE],
            $value[self::INPUT_CONTENT_LIBRARY],
            $value[self::INPUT_CONTENT_JSON]
        );
    }

    /**
     * @inheritDoc
     */
    public function withValue($value): Input
    {
        if (null === $value) {
            return $this;
        }

        if (!$value instanceof ContentEditorData) {
            throw new \LogicException('Value must be null or ' . ContentEditorData::class);
        }

        return parent::withValue([
            self::INPUT_CONTENT_LIBRARY => $value->getContentLibrary(),
            self::INPUT_CONTENT_TITLE => $value->getContentTitle(),
            self::INPUT_CONTENT_JSON => $value->getContentJson(),
            self::INPUT_CONTENT_ID => $value->getContentId(),
            self::INPUT_EDITOR_ACTION => self::EDITOR_ACTION_CREATE,
        ]);
    }

    public function withInput(InputData $post_input): Input
    {
        $clone = parent::withInput($post_input);

        $inputs = $clone->getInputs();

        $data = new ContentEditorData(
            (null !== ($id = $inputs[self::INPUT_CONTENT_ID]->getValue())) ? (int) $id : null,
            $inputs[self::INPUT_CONTENT_TITLE]->getValue(),
            $inputs[self::INPUT_CONTENT_LIBRARY]->getValue(),
            $inputs[self::INPUT_CONTENT_JSON]->getValue(),
        );

        $clone->content = new Ok($data);

        return $clone;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateOnLoadCode(): \Closure
    {
        return static function () {
        };
    }
}
