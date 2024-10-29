/**
 * Modify catalog product search
 * @category: Magento
 * @package: Perficient/Search
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Search
 */

define([
    'jquery',
    'underscore',
    'mage/template',
    'matchMedia',
    'mage/translate',
    'jquery/ui'
], function ($, _, mageTemplate, mediaCheck, $t) {

    return function (widget) {
        function isValEmpty(value) {
            return value.length === 0 || value == null || /^\s+$/.test(value);
        }


        $.widget('mage.quickSearch', widget, {
            _onPropertyChange: function () {
                var searchField = this.element,
                    clonePosition = {
                        position: 'absolute',
                        // Removed to fix display issues
                        // left: searchField.offset().left,
                        // top: searchField.offset().top + searchField.outerHeight(),
                        width: searchField.outerWidth()
                    },
                    source = this.options.template,
                    template = mageTemplate(source),
                    dropdown = $('<ul role="listbox"></ul>'),
                    value = this.element.val();

                this.submitBtn.disabled = isValEmpty(value);

                if (value.length >= parseInt(this.options.minSearchLength, 10)) {
                    $.getJSON(this.options.url, {
                        q: value
                    }, $.proxy(function (data) {
                        if (data.length) {
                            $.each(data, function (index, element) {
                                var html;

                                element.index = index;
                                html = template({
                                    data: element
                                });
                                dropdown.append(html);
                            });
                            dropdown.append('<li><a href="/catalogsearch/result/?q='+ this.element.val() +'">'+$t('View all results for "'+this.element.val()+'"')+'</a></li>');
                            this._resetResponseList(true);

                            this.responseList.indexList = this.autoComplete.html(dropdown)
                                .css(clonePosition)
                                .show()
                                .find(this.options.responseFieldElements + ':visible');

                            this.element.removeAttr('aria-activedescendant');

                            if (this.responseList.indexList.length) {
                                this._updateAriaHasPopup(true);
                            } else {
                                this._updateAriaHasPopup(false);
                            }

                            this.responseList.indexList
                                .on('click', function (e) {
                                    this.responseList.selected = $(e.currentTarget);
                                    this.searchForm.trigger('submit');
                                }.bind(this))
                                .on('mouseenter mouseleave', function (e) {
                                    this.responseList.indexList.removeClass(this.options.selectClass);
                                    $(e.target).addClass(this.options.selectClass);
                                    this.responseList.selected = $(e.target);
                                    this.element.attr('aria-activedescendant', $(e.target).attr('id'));
                                }.bind(this))
                                .on('mouseout', function (e) {
                                    if (!this._getLastElement() &&
                                        this._getLastElement().hasClass(this.options.selectClass)) {
                                        $(e.target).removeClass(this.options.selectClass);
                                        this._resetResponseList(false);
                                    }
                                }.bind(this));
                        } else {
                            this._resetResponseList(true);
                            this.autoComplete.hide();
                            this._updateAriaHasPopup(false);
                            this.element.removeAttr('aria-activedescendant');
                        }
                    }, this));
                } else {
                    this._resetResponseList(true);
                    this.autoComplete.hide();
                    this._updateAriaHasPopup(false);
                    this.element.removeAttr('aria-activedescendant');
                }
            }
        });
        return $.mage.quickSearch;
    };
});