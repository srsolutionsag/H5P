<?php

declare(strict_types=1);

/**
 * @author       Thibeau Fuhrer <thibeau@sr.solutions>
 * @noinspection AutoloadingIssuesInspection
 */
trait ilH5PTargetHelper
{
    /**
     * @var ilCtrl
     */
    protected $ctrl;

    protected function getLinkTarget(string $target_class, ?string $command, array $options = [], bool $async = false): string
    {
        $previous_options = $this->getTargetOptions($target_class);

        $this->ctrl->clearParametersByClass($target_class);

        $this->setTargetOptions($target_class, $options);

        $target = $this->ctrl->getLinkTargetByClass(
            $target_class,
            $command ?? '',
            false,
            $async
        );

        $this->setTargetOptions($target_class, $previous_options);

        return $target;
    }

    protected function getFormAction(string $target_class, string $command = null, array $options = []): string
    {
        $previous_options = $this->getTargetOptions($target_class);

        $this->ctrl->clearParametersByClass($target_class);

        $this->setTargetOptions($target_class, $options);

        $target = $this->ctrl->getFormActionByClass(
            $target_class,
            $command ?? ''
        );

        $this->setTargetOptions($target_class, $previous_options);

        return $target;
    }

    private function setTargetOptions(string $target_class, array $options): void
    {
        foreach ($options as $parameter_name => $parameter_value) {
            $this->ctrl->setParameterByClass($target_class, $parameter_name, $parameter_value);
        }
    }

    private function getTargetOptions(string $target_class): array
    {
        return $this->ctrl->getParameterArrayByClass($target_class);
    }
}
