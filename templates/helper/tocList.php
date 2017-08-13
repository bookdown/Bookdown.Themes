<?php

use Bookdown\Bookdown\Content\TocHeading;
use Bookdown\Bookdown\Content\TocHeadingIterator;

class tocListHelper
{
    /**
     * @var \Aura\Html\Helper\AnchorRaw
     */
    protected $anchorRaw;

    /**
     * @var int
     */
    protected $sublistLevelThrottle;

    /**
     * tocListHelper constructor.
     * @param \Aura\Html\Helper\AnchorRaw $anchorRaw
     */
    public function __construct(\Aura\Html\Helper\AnchorRaw $anchorRaw, \Bookdown\Bookdown\Config\RootConfig $config)
    {
        $this->anchorRaw = $anchorRaw;

        $config = ($config->get('theme'));

        /**
         * Define on which level a collapsible sublist will be created.
         * @var integer
         */
        $this->sublistLevelThrottle = isset($config->toc->collapsibleFromLevel) ? $config->toc->collapsibleFromLevel : 4;
    }

    /**
     * @param TocHeadingIterator $tocHeadings
     * @return string
     */
    public function __invoke(TocHeadingIterator $tocHeadings)
    {
        return $this->renderTocList($tocHeadings);
    }

    /**
     * @param TocHeadingIterator $tocHeadings
     * @param null $collapseId
     * @return string
     */
    protected function renderTocList(TocHeadingIterator $tocHeadings, $collapseId = null)
    {
        $return = '';

        if (null !== $collapseId) {
            $listCssClass = 'class="list-toc collapse" id="' . $collapseId . '"';
        } else {
            $listCssClass = 'class="list-toc"';
        }

        $return .= '<ul ' . $listCssClass . '>';


        foreach ($tocHeadings as $tocHeading) {

            $return .= '<li class="list-group-item">';

            $return .= '<div class="row clearfix">';
            $return .= $this->renderHeading($tocHeading);

            if ($tocHeading->getLevel() > $this->sublistLevelThrottle) {
                $collapseId = 'collapse-' . str_replace('.', '-', trim($tocHeading->getNumber(), '.'));

                if ($tocHeading->hasChildren()) {
                    $return .= '<a class="bbt-toc-toggle badge glyphicon collapsed" href="#' . $collapseId . '" data-toggle="collapse" aria-controls="' . $collapseId . '" aria-expanded="false"></a>';
                }
            }

            $return .= '</div>';

            if ($tocHeading->hasChildren()) {
                $return .= $this->renderTocList($tocHeading->getChildren(), $collapseId);
            }

            $return .= '</li>';
        }

        $return .= '</ul>';

        return $return;
    }

    /**
     * @param TocHeading $tocHeading
     * @return string
     */
    protected function renderHeading(TocHeading $tocHeading)
    {
        $return = '<div class="col-sm-3">' . '<span class="text-number">' . $tocHeading->getNumber() . '</span></div>';
        $return .= '<div class="col-sm-9">' .  $this->anchorRaw->__invoke($tocHeading->getHref(),
                $tocHeading->getTitle()) . '</div>';

        return $return;
    }
}