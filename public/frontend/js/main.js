(function ($) {
    "user strict";

    // preloader
    $(window).on("load", function () {
        $(".preloader").fadeOut(1000);
        const img = $(".bg_img");
        img.css("background-image", function () {
            const bg = "url(" + $(this).data("background") + ")";
            return bg;
        });
    });

    //Create Background Image
    (function background() {
        let img = $(".bg_img");
        img.css("background-image", function () {
            const bg = "url(" + $(this).data("background") + ")";
            return bg;
        });
    })();

    // nice-select
    $(".nice-select").niceSelect();

    $(".select2-basic").select2();
    $(".select2-multi-select").select2();
    $(".select2-auto-tokenize").select2({
        tags: true,
        tokenSeparators: [","],
    });

    // header-fixed
    const fixed_top = $(".header-section");
    $(window).on("scroll", function () {
        if ($(window).scrollTop() > 100) {
            fixed_top.addClass("animated fadeInDown header-fixed");
        } else {
            fixed_top.removeClass("animated fadeInDown header-fixed");
        }
    });

    // navbar-click
    $(".navbar li a").on("click", function () {
        const element = $(this).parent("li");
        if (element.hasClass("show")) {
            element.removeClass("show");
            element.children("ul").slideUp(500);
        } else {
            element.siblings("li").removeClass("show");
            element.addClass("show");
            element.siblings("li").find("ul").slideUp(500);
            element.children("ul").slideDown(500);
        }
    });

    //Odometer
    if ($(".statistics-item").length) {
        $(".statistics-item").each(function () {
            $(this).isInViewport(function (status) {
                if (status === "entered") {
                    for (
                        let i = 0;
                        i < document.querySelectorAll(".odometer").length;
                        i++
                    ) {
                        const el = document.querySelectorAll(".odometer")[i];
                        el.innerHTML = el.getAttribute("data-odometer-final");
                    }
                }
            });
        });
    }

    // scroll-to-top
    const ScrollTop = $(".scrollToTop");
    $(window).on("scroll", function () {
        if ($(this).scrollTop() < 100) {
            ScrollTop.removeClass("active");
        } else {
            ScrollTop.addClass("active");
        }
    });

    // faq
    $(".faq-wrapper .faq-title").on("click", function (e) {
        const element = $(this).parent(".faq-item");
        if (element.hasClass("open")) {
            element.removeClass("open");
            element.find(".faq-content").removeClass("open");
            element.find(".faq-content").slideUp(300, "swing");
        } else {
            element.addClass("open");
            element.children(".faq-content").slideDown(300, "swing");
            element
                .siblings(".faq-item")
                .children(".faq-content")
                .slideUp(300, "swing");
            element.siblings(".faq-item").removeClass("open");
            element
                .siblings(".faq-item")
                .find(".faq-title")
                .removeClass("open");
            element
                .siblings(".taq-item")
                .find(".faq-content")
                .slideUp(300, "swing");
        }
    });

    // slider
    const swiper = new Swiper(".testimonial-slider", {
        slidesPerView: 1,
        spaceBetween: 30,
        loop: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        autoplay: {
            speeds: 1000,
            delay: 2000,
        },
        speed: 2000,
        breakpoints: {
            991: {
                slidesPerView: 1,
            },
            767: {
                slidesPerView: 1,
            },
            575: {
                slidesPerView: 1,
            },
        },
    });

    // my wallet slider
    const walletSwiper = new Swiper(".wallet-slider", {
        slidesPerView: 4,
        spaceBetween: 25,
        loop: true,
        autoplay: {
            speeds: 1000,
            delay: 2000,
        },
        speed: 1000,
        breakpoints: {
            1399: {
                slidesPerView: 4,
            },
            1199: {
                slidesPerView: 3,
            },
            991: {
                slidesPerView: 3,
            },
            767: {
                slidesPerView: 2,
            },
            575: {
                slidesPerView: 1,
            },
        },
    });

    //sidebar Menu
    $(document).on("click", ".sidebar-collapse-icon", function () {
        $(".page-container").toggleClass("show");
    });

    // sidebar sub
    $(".has-sub > a").on("click", function () {
        const element = $(this).parent("li");
        if (element.hasClass("active")) {
            element.removeClass("active");
            element.children("ul").slideUp(500);
        } else {
            element.siblings("li").removeClass("active");
            element.addClass("active");
            element.siblings("li").find("ul").slideUp(500);
            element.children("ul").slideDown(500);
        }
    });

    // Mobile Menu
    $(".sidebar-mobile-menu").on("click", function () {
        $(".sidebar-main-menu").slideToggle();
    });

    //Profile Upload
    function proPicURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = $(input)
                    .parents(".preview-thumb")
                    .find(".profilePicPreview");
                $(preview).css(
                    "background-image",
                    "url(" + e.target.result + ")"
                );
                $(preview).addClass("has-image");
                $(preview).hide();
                $(preview).fadeIn(650);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(".profilePicUpload").on("change", function () {
        proPicURL(this);
    });

    $(".remove-image").on("click", function () {
        $(".profilePicPreview").css("background-image", "none");
        $(".profilePicPreview").removeClass("has-image");
    });

    $(document).ready(function () {
        const AFFIX_TOP_LIMIT = 300;
        const AFFIX_OFFSET = 110;

        const $menu = $("#menu"),
            $btn = $("#menu-toggle");

        $("#menu-toggle").on("click", function () {
            $menu.toggleClass("open");
            return false;
        });

        $(".docs-nav").each(function () {
            const $affixNav = $(this),
                $container = $affixNav.parent(),
                affixNavfixed = false,
                originalClassName = this.className,
                current = null,
                $links = $affixNav.find("a");

            function getClosestHeader(top) {
                let last = $links.first();

                if (top < AFFIX_TOP_LIMIT) {
                    return last;
                }

                for (let i = 0; i < $links.length; i++) {
                    const $link = $links.eq(i),
                        href = $link.attr("href");

                    if (href.charAt(0) === "#" && href.length > 1) {
                        const $anchor = $(href).first();

                        if ($anchor.length > 0) {
                            const offset = $anchor.offset();

                            if (top < offset.top - AFFIX_OFFSET) {
                                return last;
                            }

                            last = $link;
                        }
                    }
                }
                return last;
            }

            $(window).on("scroll", function (evt) {
                const top = window.scrollY,
                    height = $affixNav.outerHeight(),
                    max_bottom =
                        $container.offset().top + $container.outerHeight(),
                    bottom = top + height + AFFIX_OFFSET;

                if (affixNavfixed) {
                    if (top <= AFFIX_TOP_LIMIT) {
                        $affixNav.removeClass("fixed");
                        $affixNav.css("top", 0);
                        affixNavfixed = false;
                    } else if (bottom > max_bottom) {
                        $affixNav.css("top", max_bottom - height - top);
                    } else {
                        $affixNav.css("top", AFFIX_OFFSET);
                    }
                } else if (top > AFFIX_TOP_LIMIT) {
                    $affixNav.addClass("fixed");
                    affixNavfixed = true;
                }

                const $current = getClosestHeader(top);

                if (current !== $current) {
                    $affixNav.find(".active").removeClass("active");
                    $current.addClass("active");
                    current = $current;
                }
            });
        });
    });

    $(".logo-btn").click(function () {
        $(".main-side-menu").toggleClass("show");
    });
    $(".main-side-menu-cross").click(function () {
        $(".main-side-menu").removeClass("show");
    });

    $(".account-area-btn").on("click", function (e) {
        e.preventDefault();
        if ($(".account").hasClass("active")) {
            $(".account").removeClass("active");
            $(".body-overlay").removeClass("active");
        } else {
            $(".account").addClass("active");
            $(".body-overlay").addClass("active");
            $(".navbar-collapse").removeClass("show");
        }
    });
    $("#body-overlay, .account-cross-btn").on("click", function (e) {
        e.preventDefault();
        $(".account").removeClass("active");
        $(".body-overlay").removeClass("active");
    });

    $(".account-control-btn").on("click", function () {
        $(".account-wrapper").toggleClass("change-form");
    });

    // notification
    $(".notify-btn-area").click(function () {
        $(".notification-wrapper").slideToggle();
    });

    $(".header-mobile-search-btn").on("click", function (e) {
        e.preventDefault();
        if ($(".header-mobile-search-form-area").hasClass("active")) {
            $(".header-mobile-search-form-area").removeClass("active");
            $(".body-overlay").removeClass("active");
        } else {
            $(".header-mobile-search-form-area").addClass("active");
            $(".body-overlay").addClass("active");
            $(".header-section").addClass("active");
        }
    });
    $("#body-overlay").on("click", function (e) {
        e.preventDefault();
        $(".header-mobile-search-form-area").removeClass("active");
        $(".body-overlay").removeClass("active");
    });

    // active menu JS
    function splitSlash(data) {
        return data.split("/").pop();
    }
    function splitQuestion(data) {
        return data.split("?").shift().trim();
    }
    const pageNavLis = $(".sidebar-menu a");
    const dividePath = splitSlash(window.location.href);
    const divideGetData = splitQuestion(dividePath);
    const currentPageUrl = divideGetData;

    // find current sidevar element
    $.each(pageNavLis, function (index, item) {
        const anchoreTag = $(item);
        const anchoreTagHref = anchoreTag.attr("href");
        const slashIndex = anchoreTagHref.indexOf("/");
        let getUri = "";
        if (slashIndex != -1) {
            // split with /
            getUri = splitSlash(anchoreTagHref);
            getUri = splitQuestion(getUri);
        } else {
            getUri = splitQuestion(anchoreTagHref);
        }
        if (getUri == currentPageUrl) {
            const thisElementParent = anchoreTag.parents(".sidebar-menu-item");
            if (anchoreTag.hasClass("nav-link")) {
                anchoreTag.addClass("active");
            } else {
                thisElementParent.addClass("active");
            }
            const sidebarDropdown = anchoreTag.parents(".sidebar-dropdown");
            if (sidebarDropdown.length) {
                sidebarDropdown.addClass("active");
            }
            const sidebarSubmenu = thisElementParent.find(".sidebar-submenu");
            if (sidebarSubmenu.length) {
                sidebarSubmenu.slideDown("slow");
            }
            return false;
        }
    });

    //sidebar Menu
    $(".sidebar-menu-bar").on("click", function (e) {
        e.preventDefault();
        if ($(".sidebar, .navbar-wrapper, .body-wrapper").hasClass("active")) {
            $(".sidebar, .navbar-wrapper, .body-wrapper").removeClass("active");
            $(".body-overlay").removeClass("active");
        } else {
            $(".sidebar, .navbar-wrapper, .body-wrapper").addClass("active");
            $(".body-overlay").addClass("active");
        }
    });
    $("#body-overlay").on("click", function (e) {
        e.preventDefault();
        $(".sidebar, .navbar-wrapper, .body-wrapper").removeClass("active");
        $(".body-overlay").removeClass("active");
    });

    $(".sidebar-search-input").on("click", function (e) {
        e.preventDefault();
        $(".sidebar-search-box").addClass("d-block");
        $(".body-overlay").addClass("active");
    });
    $("#body-overlay").on("click", function (e) {
        e.preventDefault();
        $(".sidebar-search-box").addClass("d-none");
        $(".body-overlay").removeClass("active");
    });

    // dashboard-list
    $(".dashboard-list-item").on("click", function (e) {
        const element = $(this).parent(".dashboard-list-item-wrapper");
        if (element.hasClass("show")) {
            element.removeClass("show");
            element.find(".preview-list-wrapper").removeClass("show");
            element.find(".preview-list-wrapper").slideUp(300, "swing");
        } else {
            element.addClass("show");
            element.children(".preview-list-wrapper").slideDown(300, "swing");
            element
                .siblings(".dashboard-list-item-wrapper")
                .children(".preview-list-wrapper")
                .slideUp(300, "swing");
            element
                .siblings(".dashboard-list-item-wrapper")
                .removeClass("show");
            element
                .siblings(".dashboard-list-item-wrapper")
                .find(".dashboard-list-item")
                .removeClass("show");
            element
                .siblings(".dashboard-list-item-wrapper")
                .find(".preview-list-wrapper")
                .slideUp(300, "swing");
        }
    });

    //sidebar Menu
    $(document).on("click", "#notification-icon", function () {
        $(".push-wrapper").toggleClass("active");
    });

    //info-btn
    $(document).on("click", ".info-btn", function () {
        $(".support-profile-wrapper").addClass("active");
    });

    $(document).on("click", ".chat-cross-btn", function () {
        $(".support-profile-wrapper").removeClass("active");
    });

    $(".cart-btn").on("click", function (e) {
        e.preventDefault();
        if ($(".cart").hasClass("active")) {
            $(".cart").removeClass("active");
            $(".body-overlay").removeClass("active");
        } else {
            $(".cart").addClass("active");
            $(".body-overlay").addClass("active");
        }
    });
    $("#body-overlay, .cart-wrapper .cross-btn").on("click", function (e) {
        e.preventDefault();
        $(".cart").removeClass("active");
        $(".body-overlay").removeClass("active");
    });

    $(document).on("click", ".cart-item .remove-btn", function (e) {
        e.preventDefault();
        $(this).parent().hide(300);
    });

    // lightcase
    $(window).on("load", function () {
        $("a[data-rel^=lightcase]").lightcase();
    });

    $(".dash-payment-title-area").click(function () {
        $(this)
            .parents(".buy-coin-form .dash-payment-item-wrapper")
            .find(".dash-payment-item")
            .toggleClass("active");
    });

    $(".dash-payment-title-area").click(function () {
        $(this)
            .parents(".body-wrapper .dash-payment-item-wrapper")
            .find(".dash-payment-item")
            .toggleClass("active");
    });

    $(".confirm-withdraw-method-item.proceed").click(function () {
        $(".confirm-withdraw-form").slideToggle();
        $(this).toggleClass("active");
    });

    // input toggle
    $("#visa").click(function () {
        $(".checkout-hedden-form").addClass("active");
    });
    $("#paypal").click(function () {
        $(".checkout-hedden-form").removeClass("active");
    });
    $("#skrill").click(function () {
        $(".checkout-hedden-form").removeClass("active");
    });

    // filter
    $(".finter-btn").click(function () {
        $(".filter-form").slideToggle();
        $(".finter-btn").toggleClass("active");
    });

    // input toggle
    $("#visa").click(function () {
        $(".visa-form").addClass("active");
    });
    $("#paypal").click(function () {
        $(".visa-form").removeClass("active");
    });
    $("#stripe").click(function () {
        $(".visa-form").removeClass("active");
    });

    $("form button[type=submit], form input[type=submit]").on(
        "click",
        function (event) {
            const inputFileds = $(this)
                .parents("form")
                .find(
                    "input[type=text], input[type=number], input[type=email], input[type=password]"
                );
            let mode = false;
            $.each(inputFileds, function (index, item) {
                if ($(item).attr("required") != undefined) {
                    if ($(item).val() == "") {
                        mode = true;
                    }
                }
            });
            if (mode == false) {
                $(this).parents("form").find(".btn-ring").show();
                $(this)
                    .parents("form")
                    .find("button[type=submit],input[type=submit]")
                    .prop("disabled", true);
                $(this).parents("form").submit();
            }
        }
    );

    $(document).ready(function () {
        $.each($(".btn-loading"), function (index, item) {
            $(item).append(`<span class="btn-ring"></span>`);
        });
    });
})(jQuery);

//************* Pop Up Modal  ***************/
function openAlertModal(
    URL,
    target,
    message,
    actionBtnText = "Remove",
    httpMethod = "DELETE"
) {
    if (URL == "" || target == "") {
        return false;
    }

    if (message == "") {
        message = "Are you sure to delete ?";
    }

    const methodInput = `<input type="hidden" name="_method" value="${httpMethod}">`;
    openModalByContent({
        content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${methodInput}
                                <div class="head mb-3" style="color: #ffffff">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                    <input type="hidden" name="type" value="${actionBtnText}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info rounded text-light">Close</button>
                                    <button type="submit" class="alert-submit-btn btn btn--danger btn-loading rounded text-light">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
    });
}

function openModalByContent(
    data = {
        content: "",
        animation: "mfp-move-horizontal",
        size: "medium",
    }
) {
    $.magnificPopup.open({
        removalDelay: 500,
        items: {
            src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"
                }">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
        },
        callbacks: {
            beforeOpen: function () {
                this.st.mainClass = data.animation ?? "mfp-move-horizontal";
            },
            open: function () {
                const modalCloseBtn =
                    this.contentContainer.find(".modal-close");
                $(modalCloseBtn).click(function () {
                    $.magnificPopup.close();
                });
            },
        },
        midClick: true,
    });
}

$(document).ready(function () {
    $(".show_hide_password .show-pass").on("click", function (event) {
        event.preventDefault();
        if ($(this).parent().find("input").attr("type") == "text") {
            $(this).parent().find("input").attr("type", "password");
            $(this).find("i").addClass("fa-eye-slash");
            $(this).find("i").removeClass("fa-eye");
        } else if ($(this).parent().find("input").attr("type") == "password") {
            $(this).parent().find("input").attr("type", "text");
            $(this).find("i").removeClass("fa-eye-slash");
            $(this).find("i").addClass("fa-eye");
        }
    });
});

/**
 * Function For Get All Country list by AJAX Request
 * @param {HTML DOM} targetElement
 * @param {Error Place Element} errorElement
 * @returns
 */
let allCountries = "";
function getAllCountries(
    hitUrl,
    targetElement = $(".country-select"),
    errorElement = $(".country-select").siblings(".select2")
) {
    if (targetElement.length == 0) {
        return false;
    }
    const CSRF = $("meta[name=csrf-token]").attr("content");
    const data = {
        _token: CSRF,
    };
    $.post(hitUrl, data, function () {
        // success
        $(errorElement).removeClass("is-invalid");
        $(targetElement).siblings(".invalid-feedback").remove();
    })
        .done(function (response) {
            // Place States to States Field
            let options = "<option selected disabled>Select Country</option>";
            let selected_old_data = "";
            if ($(targetElement).attr("data-old") != null) {
                selected_old_data = $(targetElement).attr("data-old");
            }
            $.each(response, function (index, item) {
                options += `<option value="${item.name}" data-id="${item.id
                    }" data-mobile-code="${item.mobile_code}" ${selected_old_data == item.name ? "selected" : ""
                    }>${item.name}</option>`;
            });

            allCountries = response;

            $(targetElement).html(options);
        })
        .fail(function (response) {
            const faildMessage = 'Something went wrong! Please try again.';
            const faildElement = `<span class="invalid-feedback" role="alert">
                                <strong>${faildMessage}</strong>
                            </span>`;
            $(errorElement).addClass("is-invalid");
            if ($(targetElement).siblings(".invalid-feedback").length != 0) {
                $(targetElement)
                    .siblings(".invalid-feedback")
                    .text(faildMessage);
            } else {
                errorElement.after(faildElement);
            }
        });
}
// getAllCountries();

/**
 * Function for reload the all countries that already loaded by using getAllCountries() function.
 * @param {string} targetElement
 * @param {string} errorElement
 * @returns
 */
function reloadAllCountries(
    targetElement,
    errorElement = $(".country-select").siblings(".select2")
) {
    if (allCountries == "" || allCountries == null) {
        // alert();
        return false;
    }
    let options = "<option selected disabled>Select Country</option>";
    let selected_old_data = "";
    if ($(targetElement).attr("data-old") != null) {
        selected_old_data = $(targetElement).attr("data-old");
    }
    $.each(allCountries, function (index, item) {
        options += `<option value="${item.name}" data-id="${item.id
            }" data-currency-name="${item.currency_name}" data-currency-code="${item.currency_code
            }" data-currency-symbol="${item.currency_symbol}" ${selected_old_data == item.name ? "selected" : ""
            }>${item.name}</option>`;
    });
    $(targetElement).html(options);
}

function placePhoneCode(code) {
    if (code != undefined) {
        code = code.replace("+", "");
        code = "+" + code;
        $("input.phone-code").val(code);
        $("div.phone-code").html(code);
    }
}

/**
 * Function for search user panel sidebar menu item
 */
function sideBarSearch() {
    const menuLinks = $(".sidebar-menu a");
    const filterMenuItem = [];
    $.each(menuLinks, function (index, item) {
        if ($(item).attr("href") != "javascript:void(0)") {
            filterMenuItem.push(item);
        }
    });
    $(".sidebar-search-input").keyup(function () {
        sideBarSearchWithInput($(this), filterMenuItem);
    });
}
sideBarSearch();
function sideBarSearchWithInput(input, navItems) {
    const inputValue = input.val().toLowerCase();
    const searchResult = [];
    $.each(navItems, function (index, item) {
        const title = $(item).find("span").text().toLowerCase();
        const result = title.match(inputValue);
        if (result != null) {
            searchResult.push(item);
        }
    });
    $(".sidebar-search-result").html("");
    let singleItem = "";
    $.each(searchResult, function (index, item) {
        // console.log(item)
        const link = $(item).attr("href");
        const title = $(item).find("span").text();
        const iconClass = $(item).find("i").attr("class");
        singleItem += `<div class="single-item">
                                        <a href="${link}">
                                        <i class="${iconClass}"></i>
                                        <span style="position:inherit">${title}</span>
                                        </a>
                                </div>`;
    });

    const menu_box = `<div class="sidebar-search-box">` + singleItem + `</div>`;
    $(".sidebar-search-result").append(menu_box);
}
