<?php

declare(strict_types=1);

use srag\Plugins\H5P\Content\IContentRepository;
use srag\Plugins\H5P\Content\IContentUserData;
use srag\Plugins\H5P\Content\IContent;
use srag\Plugins\H5P\Content\ContentEditorData;

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
class ilH5PContentRepository implements IContentRepository
{
    use ilH5PActiveRecordHelper;

    /**
     * @var ilObjUser
     */
    protected $user;

    /**
     * @var ilDBInterface
     */
    protected $database;

    public function __construct(ilObjUser $user, ilDBInterface $database)
    {
        $this->user = $user;
        $this->database = $database;
    }

    public function deleteContent(IContent $content): void
    {
        $this->abortIfNoActiveRecord($content);

        $content->delete();

        $this->reSortContents($content->getObjId());
    }

    public function getContent(int $content_id): ?IContent
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where(["content_id" => $content_id])->first();
    }

    public function getFirstContentOf(int $obj_id): ?IContent
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where([
            'obj_id' => $obj_id,
        ], '=')->orderBy('sort')->first();
    }

    public function getNextContentOf(int $obj_id, int $content_id): ?IContent
    {
        if (null === ($content = $this->getContent($content_id))) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where([
            'obj_id' => $obj_id,
            'sort' => $content->getSort(),
        ], [
            'obj_id' => '=',
            'sort' => '>',
        ])->orderBy('sort')->first();
    }

    public function getPreviousContentOf(int $obj_id, int $content_id): ?IContent
    {
        if (null === ($content = $this->getContent($content_id))) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where([
            'obj_id' => $obj_id,
            'sort' => $content->getSort(),
        ], [
            'obj_id' => '=',
            'sort' => '<',
        ])->orderBy('sort', 'DESC')->first();
    }

    /**
     * @inheritDoc
     */
    public function getContentsByLibrary(int $library_id): array
    {
        return ilH5PContent::where(["library_id" => $library_id])->get();
    }

    /**
     * @inheritDoc
     */
    public function getContentsByObject(int $obj_id): array
    {
        $h5p_contents = ilH5PContent::where([
            "obj_id" => $obj_id,
        ])->orderBy("sort", "asc")->get();

        // Fix index with array_values
        return array_values($h5p_contents);
    }

    public function getContentBySlug(string $slug): ?IContent
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where(["slug" => $slug])->first();
    }

    /**
     * @inheritDoc
     */
    public function getUnfilteredContents(): array
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContent::where(["filtered" => ""])->get();
    }

    public function cloneContent(IContent $content): IContent
    {
        $this->abortIfNoActiveRecord($content);

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $content->copy();
    }

    public function getNumberOfAuthors(): int
    {
        $result = $this->database->query(
            "SELECT COUNT(DISTINCT content_user_id) AS cnt FROM " . ilH5PContent::TABLE_NAME,
        );

        return (int) $result->fetchAssoc()["cnt"];
    }

    public function getUserData(
        int $content_id,
        string $data_type,
        int $sub_content_id,
        int $user_id
    ): ?ilH5PContentUserData {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContentUserData::where([
            "content_id" => $content_id,
            "data_id" => $data_type,
            "user_id" => $user_id,
            "sub_content_id" => $sub_content_id
        ])->first();
    }

    /**
     * @inheritDoc
     */
    public function getUserDataByContent(int $content_id): array
    {
        return ilH5PContentUserData::where(["content_id" => $content_id])->get();
    }

    /**
     * @inheritDoc
     */
    public function getUserDataByContentAndUser(int $content_id, int $user_id): array
    {
        return ilH5PContentUserData::where([
            "content_id" => $content_id,
            "user_id" => $user_id,
        ])->get();
    }

    public function getContentStateOfUser(int $content_id, int $user_id): ?IContentUserData
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return ilH5PContentUserData::where([
            'content_id' => $content_id,
            'user_id' => $user_id,
            'data_id' => 'state',
            'preload' => 1
        ], '=')->first();
    }

    /**
     * @inheritDoc
     */
    public function getContentStatesByObject(int $obj_id): array
    {
        return ilH5PContentUserData::innerjoin(
            ilH5PContent::TABLE_NAME,
            "content_id",
            "content_id"
        )->where([
            ilH5PContent::TABLE_NAME . '.obj_id' => $obj_id,
            ilH5PContentUserData::TABLE_NAME . '.data_id' => 'state',
        ], '=')->get();
    }

    /**
     * @inheritDoc
     */
    public function getContentStatesByObjectAndUser(int $obj_id, int $user_id): array
    {
        return ilH5PContentUserData::innerjoin(
            ilH5PContent::TABLE_NAME,
            "content_id",
            "content_id"
        )->where([
            ilH5PContent::TABLE_NAME . '.obj_id' => $obj_id,
            ilH5PContentUserData::TABLE_NAME . '.user_id' => $user_id,
            ilH5PContentUserData::TABLE_NAME . '.data_id' => 'state',
        ], '=')->get();
    }

    public function moveContentDown(int $content_id, int $obj_id): void
    {
        $h5p_content = $this->getContent($content_id);

        if ($h5p_content !== null) {
            $h5p_content->setSort($h5p_content->getSort() + 15);

            $this->abortIfNoActiveRecord($h5p_content);
            $this->storeContent($h5p_content);

            $this->reSortContents($obj_id);
        }
    }

    public function moveContentUp(int $content_id, int $obj_id): void
    {
        $h5p_content = $this->getContent($content_id);

        if ($h5p_content !== null) {
            $h5p_content->setSort($h5p_content->getSort() - 15);

            $this->abortIfNoActiveRecord($h5p_content);
            $this->storeContent($h5p_content);

            $this->reSortContents($obj_id);
        }
    }

    public function storeContent(IContent $content): void
    {
        $this->abortIfNoActiveRecord($content);

        $time = time();
        if (empty($content->getContentId())) {
            $content->setCreatedAt($time);
            $content->setContentUserId($this->user->getId());
            $content->setSort((count($this->getContentsByObject($content->getObjId())) + 1) * 10);
        }

        $content->setUpdatedAt($time);
        $content->store();
    }

    public function storeUserData(IContentUserData $user_data): void
    {
        $this->abortIfNoActiveRecord($user_data);
        $time = time();

        if (empty($user_data->getId())) {
            $user_data->setCreatedAt($time);
            $user_data->setUserId($this->user->getId());
        }

        $user_data->setUpdatedAt($time);
        $user_data->store();
    }

    public function deleteUserData(IContentUserData $user_data): void
    {
        $this->abortIfNoActiveRecord($user_data);

        $user_data->delete();
    }

    protected function reSortContents(int $obj_id): void
    {
        $h5p_contents = $this->getContentsByObject($obj_id);

        $i = 1;
        foreach ($h5p_contents as $h5p_content) {
            $h5p_content->setSort($i * 10);
            $this->storeContent($h5p_content);
            $i++;
        }
    }
}
