<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Perficient\Base\Helper\Element\Html;

/**
 * Date element block
 */
class Date extends \Magento\Framework\View\Element\Html\Date
{
    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        $html = '<input type="text" name="' . $this->getName() . '" id="' . $this->getId() . '" ';
        $html .= 'value="' . $this->escapeHtml($this->getValue()) . '" ';
        $html .= 'class="' . $this->getClass() . '" ' . $this->getExtraParams() . '/> ';
        $calendarYearsRange = $this->getYearsRange();
        $changeMonth = $this->getChangeMonth();
        $changeYear = $this->getChangeYear();
        $maxDate = $this->getMaxDate();
        $showOn = $this->getShowOn();
        $firstDay = $this->getFirstDay();

        $html .= '<script type="text/javascript">
            require(["jquery", "mage/calendar"], function($){
                    $("#' .
            $this->getId() .
            '").calendar({
                        showsTime: ' .
            ($this->getTimeFormat() ? 'true' : 'false') .
            ',
                        ' .
            ($this->getTimeFormat() ? 'timeFormat: "' .
                $this->getTimeFormat() .
                '",' : '') .
            '
                        dateFormat: "' .
            $this->getDateFormat() .
            '",
                        buttonImage: "' .
            $this->getImage() .
            '",
                        ' .
            ($calendarYearsRange ? 'yearRange: "' .
                $calendarYearsRange .
                '",' : '') .
            '
                        buttonText: "' .
            (string)new \Magento\Framework\Phrase(
                'Select Date'
            ) .
            '"' . ($maxDate ? ', maxDate: "' . $maxDate . '"' : '') .
            ($changeMonth === null ? '' : ', changeMonth: ' . $changeMonth) .
            ($changeYear === null ? '' : ', changeYear: ' . $changeYear) .
            ($showOn ? ', showOn: "' . $showOn . '"' : '') .
            ($firstDay ? ', firstDay: ' . $firstDay : '') . ', beforeShow'  .': function(){
            setTimeout(function () {
                 $( "<label class=\'no-label\' for=\'datepicker-month\'>Select Month</label>" ).insertBefore( ".ui-datepicker-month" );
                 $( "<label class=\'no-label\' for=\'datepicker-year\'>Select Year</label>" ).insertBefore( ".ui-datepicker-year" );
                 $(".ui-datepicker-month").attr("id", "datepicker-month" );
                 $(".ui-datepicker-year").attr("id", "datepicker-year" );
                 $(".ui-datepicker-next, .ui-datepicker-prev").removeAttr("title").attr({"tabindex":"0","role":"button"});
                }, 0);
             }'.
            '})
            });
            </script>';

        return $html;
    }
}
