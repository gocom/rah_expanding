<?php

/*
 * Rah_expanding - Plugin for Textpattern CMS
 * http://rahforum.biz/plugins/rah_expanding
 *
 * Copyright (C) 2013 Jukka Svahn
 *
 * This file is part of rah_expanding.
 *
 * Rah_expanding is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, version 2.
 *
 * Rah_expanding is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Rah_expanding. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * The plugin class.
 */

class Rah_Expanding
{
    /**
     * Constructor.
     */

    public function __construct()
    {
        register_callback(array($this, 'jquery'), 'admin_side', 'head_end');
        register_callback(array($this, 'initialize'), 'admin_side', 'head_end');
    }

    /**
     * Initializes the JavaScript.
     */

    public function initialize()
    {
        $js = <<<EOF
            $(function ()
            {
                $('textarea:not(.rah_expanding_disable)').rah_expanding();
            });
EOF;

        echo script_js($js);
    }

    /**
      * The resizer JavaScript.
      */

    public function jquery()
    {
        $js = <<<EOF
            /**
             * Forked from Jack Moore's Autosize project.
             *
             * Autosize 1.10 - jQuery plugin for textareas
             * by Jack Moore
             * <http://www.jacklmoore.com/autosize>
             *
             * (c) 2012 Jack Moore - jacklmoore.com
             * license: www.opensource.org/licenses/mit-license.php
             */

            (function ($)
            {
                var test = $('<textarea/>').attr('oninput', 'return').css('line-height', '99px');

                if (
                    $.isFunction(test.prop('oninput')) === false ||
                    test.css('line-height') !== '99px'
                )
                {
                    $.fn.rah_expanding = function ()
                    {
                        return this;
                    };
                    return;
                }

                $.fn.rah_expanding = function ()
                {
                    var copy = '<textarea tabindex="-1" style="position:absolute; top:-9999px; left:-9999px; right:auto; bottom:auto; border:0; padding:0; -moz-box-sizing:content-box; -webkit-box-sizing:content-box; box-sizing:content-box; word-wrap:break-word; height:0 !important; min-height:0 !important; min-width:0 !important; overflow:hidden">',

                    copyStyle = [
                        'font-family',
                        'font-size',
                        'font-weight',
                        'font-style',
                        'font-variant',
                        'letter-spacing',
                        'text-transform',
                        'word-spacing',
                        'text-indent',
                        'line-height',
                        'tab-size',
                        'text-align',
                        'text-rendering',
                        'zoom'
                    ];

                    return this.each(function ()
                    {
                        var textarea = $(this);

                        if (textarea.data('rah_expanding_mirror') || textarea.data('rah_expanding_is_mirror'))
                        {
                            return;
                        }

                        var opt =
                        {
                            min : textarea.height(),
                            max : parseInt(textarea.css('max-height'), 10),
                            offset : 0,
                            mirror : $(copy).data('rah_expanding_is_mirror', true),
                            active : false
                        };

                        if (!opt.max || opt.max < 0 || opt.max > 99999)
                        {
                            opt.max = 99999;
                        }

                        if (opt.max <= opt.min)
                        {
                            return;
                        }

                        if (
                            textarea.css('box-sizing') === 'border-box' || 
                            textarea.css('-moz-box-sizing') === 'border-box'
                        )
                        {
                            opt.offset = textarea.outerHeight() - textarea.height();
                        }

                        textarea.data('rah_expanding_mirror', opt.mirror).css(
                        {
                            'overflow'   : 'hidden',
                            'overflow-x' : 'hidden',
                            'overflow-y' : 'hidden',
                            'word-wrap'  : 'break-word',
                            'resize'     : 'none'
                        });

                        var methods =
                        {
                            resize : function ()
                            {
                                if (opt.active)
                                {
                                    return;
                                }

                                opt.active = true;

                                var height = Math.max(opt.min, Math.min(opt.max, 
                                    opt.mirror
                                    .val(textarea.val())
                                    .css({
                                        'overflow-y' : textarea.css('overflow-y'),
                                        'width' : textarea.width() + 'px'
                                    })
                                    .scrollTop(0)
                                    .scrollTop(99999)
                                    .scrollTop()
                                ));

                                textarea.css({
                                    'overflow-y' : height < opt.max ? 'hidden' : 'scroll',
                                    'height' : (height + opt.offset) + 'px',
                                    'max-height' : (height + opt.offset) + 'px',
                                    'min-height' : (height + opt.offset) + 'px'
                                });

                                setTimeout(function () {
                                    opt.active = false;
                                }, 1);
                            },
                            copyStyles : function ()
                            {
                                $.each(copyStyle, function (key, value)
                                {
                                    var rule = textarea.css(value);
                                    opt.mirror.css(value, rule);
                                    textarea.css(value, rule);
                                });

                                $.each(['rows', 'cols'], function (key, value)
                                {
                                    opt.mirror.attr(value, textarea.attr(value));
                                });
                            }
                        };

                        methods.copyStyles();
                        $('body').append(opt.mirror);
                        textarea.on('input keyup blur focus resize rah_expanding_resize', methods.resize);
                        $(window).on('orientationchange resize', methods.resize);

                        var val = textarea.val();
                        textarea.val('');
                        textarea.val(val);

                        methods.resize();
                    });
                };
            }(jQuery));
EOF;

        echo script_js($js);
    }
}

new Rah_Expanding();
