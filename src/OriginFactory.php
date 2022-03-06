<?php

namespace AndrewNicols\MoodleRay;

use Spatie\Backtrace\Frame;
use Spatie\Ray\Origin\DefaultOriginFactory;

class OriginFactory extends DefaultOriginFactory
{
    /**
     * @return \AndrewNicols\MoodleRay\Spatie\Backtrace\Frame|null
     */
    public function getFrame()
    {
        $frames = $this->getAllFrames();

        $indexOfRay = $this->getIndexOfRayFrame($frames);

        /** @var Frame $rayFrame */
        $rayFrame = $frames[$indexOfRay] ?? null;

        $searchFrame = $frames[$indexOfRay + 1] ?? null;

        if (! $rayFrame) {
            return null;
        }

        $indexOfDmlFrame = $this->getIndexOfDmlFrame($frames);
        if ($indexOfDmlFrame) {
            return $frames[$indexOfDmlFrame + 1];
        }

        if (strpos($rayFrame->file, 'ray/vendor/autoload.php') !== false && $rayFrame->method === 'ray') {
            return $frames[$indexOfRay + 1];
        }

        return $rayFrame;
    }


    protected function getIndexOfDmlFrame(array $frames)
    {
        $index = $this->search(function (Frame $frame) {
            if (substr($frame->class, -15) === 'moodle_database') {
                return true;
            }
            if ($frame->class === Ray::class) {
                return true;
            }

            if ($this->startsWith($frame->file, dirname(__DIR__))) {
                return true;
            }

            return false;
        }, $frames);

        return $index + 1;
    }
}
