// =========================================================
//  Light Bootstrap Dashboard - v2.0.1
// =========================================================
//
//  Product Page: https://www.creative-tim.com/product/light-bootstrap-dashboard-laravel
//  Copyright 2019 Creative Tim (https://www.creative-tim.com) & Updivision (https://www.updivision.com)
//  Licensed under MIT (https://github.com/creativetimofficial/light-bootstrap-dashboard-laravel/blob/master/LICENSE)
//
//  Coded by Creative Tim & Updivision
//
// =========================================================
//
//  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

var searchVisible = 0;
var transparent = true;

var transparentDemo = true;
var fixedTop = false;

var navbar_initialized = false;
var mobile_menu_visible = false;
let mobile_menu_initialized = false;
let toggle_initialized = false;
let bootstrap_nav_initialized = false;
let $sidebar;
let isWindows;

let domMobileSideMenu = $('#mobileSideMenu');

let inMobileModeFlag = false;

$(document).ready(function () {
    let window_width = $(window).width();

    // lbd.getTopNavContent(); // Grab the Top Navigation ONCE

    // check if there is an image set for the sidebar's background
    lbd.checkSidebarImage();

    // Init navigation toggle for small screens
    if (window_width <= 991) {
        lbd.initRightMenu();
    }

    //  Activate the tooltips
    $('[rel="tooltip"]').tooltip();

    //      Activate regular switches
    if ($("[data-toggle='switch']").length != 0) {
        $("[data-toggle='switch']").bootstrapSwitch();
    }

    $('.form-control').on("focus", function() {
        $(this).parent('.input-group').addClass("input-group-focus");
    }).on("blur", function() {
        $(this).parent(".input-group").removeClass("input-group-focus");
    });

    // Fixes sub-nav not working as expected on IOS
    $('body').on('touchstart.dropdown', '.dropdown-menu', function(e) {
        e.stopPropagation();
    });
});

// activate collapse right menu when the windows is resized
$(window).resize(function() {
    if ($(window).width() >= 991) {
        inMobileModeFlag = false;
        domMobileSideMenu.addClass('d-none');
        domMobileSideMenu.html('');
    }
    lbd.initRightMenu();
});

lbd = {
    misc: {
        navbar_menu_visible: 0
    },
    checkSidebarImage: function() {
        $sidebar = $('.sidebar');
        image_src = $sidebar.data('image');

        if (image_src !== undefined) {
            sidebar_container = '<div class="sidebar-background" style="background-image: url(' + image_src + ') "/>'
            $sidebar.append(sidebar_container);
        } else if (mobile_menu_initialized == true) {
            // reset all the additions that we made for the sidebar wrapper only if the screen is bigger than 991px
            $sidebar_wrapper.find('.navbar-form').remove();
            $sidebar_wrapper.find('.nav-mobile-menu').remove();

            mobile_menu_initialized = false;
        }
    },

    getTopNavContent: function () {
        let sidebar_wrapper = $('.sidebar-wrapper');
        let navbar = $('nav').find('.navbar-collapse').first().clone(true);
        let nav_content = '';
        let mobile_menu_content = '';

        //add the content from the regular header to the mobile menu
        navbar.children('ul').each(function () {
            let content_buff = $(this).html();
            nav_content = nav_content + content_buff;
        });
        nav_content = '<ul class="nav nav-mobile-menu">' + nav_content + '</ul>';

        let navbar_form = $('nav').find('.navbar-form').clone(true);
        let sidebar_nav = sidebar_wrapper.find(' > .nav');

        // insert the navbar form before the sidebar list
       
        domMobileSideMenu.html(nav_content);
    },

    /** Simplified Versions */
    initRightMenu: function (width) {
       
        if ($(window).width() > 991) {
            // Not Mobile
           
        } else {
            if (!inMobileModeFlag) {
               
                inMobileModeFlag = true;
                domMobileSideMenu.removeClass('d-none');
               
                this.getTopNavContent();
                this.displayRightMenu();
                // This is Mobile
            } 
        }
    },

    displayRightMenu: function (){
        if (!toggle_initialized) {
            let $toggle = $('.navbar-toggler');
            $toggle.click(function() {
                if (mobile_menu_visible === true) {
                    $('html').removeClass('nav-open');
                    $('.close-layer').remove();
                    setTimeout(function() {
                        $toggle.removeClass('toggled');
                    }, 400);
                    mobile_menu_visible = false;
                } else {
                    setTimeout(function() {
                        $toggle.addClass('toggled');
                    }, 430);
                   let  main_panel_height = $('.main-panel')[0].scrollHeight;
                    $layer = $('<div class="close-layer"></div>');
                    $layer.css('height', main_panel_height + 'px');
                    $layer.appendTo(".main-panel");
                    setTimeout(function() {
                        $layer.addClass('visible');
                    }, 100);
                    $layer.click(function() {
                        $('html').removeClass('nav-open');
                        mobile_menu_visible = false;
                        $layer.removeClass('visible');
                        setTimeout(function() {
                            $layer.remove();
                            $toggle.removeClass('toggled');
                        }, 400);
                    });
                    $('html').addClass('nav-open');
                    mobile_menu_visible = true;
                }
            });
            toggle_initialized = true;
        }
    },
}

// Returns a function, that, as long as it continues to be invoked, will not
// be triggered. The function will be called after it stops being called for
// N milliseconds. If `immediate` is passed, trigger the function on the
// leading edge, instead of the trailing.

function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this,
            args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
    };
}
