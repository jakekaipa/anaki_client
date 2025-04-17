$(document).ready(function(){
	// .header 요소 선택
    var header = document.querySelector('.header');

    // header 요소가 존재하는지 확인
    if (header) {
        // btn-mm 버튼에 이벤트 리스너 추가
        document.querySelector('.btn-mm').addEventListener('click', function() {
            // header 요소와 gnb 요소 선택
            var btnMm = this; // 이미 btn-mm 버튼을 가리키므로 this 키워드 사용
            var gnb = document.querySelector('.gnb');

            // btn-mm에 open 클래스가 있는지 확인하고, 있으면 제거, 없으면 추가
            if (btnMm.classList.contains('open')) {
                btnMm.classList.remove('open');
                gnb.classList.remove('open');  // gnb에서도 open 클래스 제거
            } else {
                btnMm.classList.add('open');
                gnb.classList.add('open');  // gnb에도 open 클래스 추가
            }
        });
    }

	// 언어 선택
	// initializeLanguageSelector();

	// 사이드메뉴 토글
	$('.p-head__menu-btn').on('click', function () {
		$('body').toggleClass('side-hide');
	});
});

var lastDeviceType = null; // 이전 디바이스 타입을 저장하는 변수

// 함수를 정의하여 현재 창의 너비를 체크하고 모바일인지 PC인지 판단합니다.
function checkDevice() {
    var width = window.innerWidth;
    var currentDeviceType = width <= 1024 ? 'mobile' : 'desktop';

    // 이전 디바이스 타입과 현재 디바이스 타입이 같으면 함수 실행을 중지
    if (currentDeviceType === lastDeviceType) return;

    // 현재 디바이스 타입을 lastDeviceType 변수에 저장
    lastDeviceType = currentDeviceType;

    if (currentDeviceType === 'mobile') {
        console.log('모바일 사이즈입니다.');
        $('body').removeClass('desktop').addClass('mobile');
        // 모바일용 스크립트를 활성화 (PC용 이벤트 해제)
        $('.header').off('mouseenter mouseleave');
    } else {
        console.log('PC 사이즈입니다.');
        $('body').removeClass('mobile').addClass('desktop');
        // PC 사이즈에 맞는 스크립트를 실행
        $('.header').on('mouseenter', function () {
            $(this).find('.gnb-sub').stop().slideDown(200);
        });
        $('.header').on('mouseleave', function () {
            $(this).find('.gnb-sub').stop().slideUp(200);
        });
        $('.mo-gnb-wrap').hide();
    }
}

// 텍스트 입력요소
class CommonInputText {
    constructor() {
        this.inputs = document.querySelectorAll('.i-txt__input');
        this.deleteButtons = document.querySelectorAll('.i-txt__del');  // 삭제 버튼 선택
        this.lookButtons = document.querySelectorAll('.i-txt__btn-look'); // 비밀번호 보기 버튼 선택
        this.initEvents();
    }

    initEvents() {
        // 입력 필드에 대한 이벤트 리스너 추가
        this.inputs.forEach(input => {
            input.addEventListener('input', () => {
                // 만약 삭제 버튼이 존재하면
                const deleteButton = input.closest('.i-txt__wrap').querySelector('.i-txt__del');
                if (deleteButton) {
                    if (input.value.trim() !== '') {
                        deleteButton.classList.add('active');
                    } else {
                        deleteButton.classList.remove('active');
                    }
                }
            });
        });

        // 삭제 버튼에 대한 이벤트 리스너 추가
        this.deleteButtons.forEach(deleteButton => {
            deleteButton.addEventListener('click', () => {
                const input = deleteButton.closest('.i-txt__wrap').querySelector('.i-txt__input');
                input.value = '';  // 입력 필드의 값을 비우기
                deleteButton.classList.remove('active');  // active 클래스 제거
            });
        });

        // 비밀번호 보기 버튼에 대한 이벤트 리스너 추가
        this.lookButtons.forEach(button => {
            button.addEventListener('click', () => {
                const input = button.closest('.i-txt__wrap').querySelector('.i-txt__input');
                if (input.type === 'password') {
                    input.type = 'text';
                    button.classList.add('looking');
                } else {
                    input.type = 'password';
                    button.classList.remove('looking');
                }
            });
        });
    }
}

// 상단으로 이동
function goTop() {
	$('html, body').animate({scrollTop: 0});
}

$(window).on('load resize', function() {
	checkDevice();

	new CommonInputText();
});

$.datepicker.regional["ko"] = {
	closeText: "닫기",
	prevText: "이전달",
	nextText: "다음달",
	currentText: "오늘",
	monthNames: ["1월(JAN)","2월(FEB)","3월(MAR)","4월(APR)","5월(MAY)","6월(JUN)", "7월(JUL)","8월(AUG)","9월(SEP)","10월(OCT)","11월(NOV)","12월(DEC)"],
	monthNamesShort: ["1월","2월","3월","4월","5월","6월", "7월","8월","9월","10월","11월","12월"],
	dayNames: ["일","월","화","수","목","금","토"],
	dayNamesShort: ["일","월","화","수","목","금","토"],
	dayNamesMin: ["일","월","화","수","목","금","토"],
	weekHeader: "Wk",
	dateFormat: "yy-mm-dd",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: ""
};
$.datepicker.setDefaults($.datepicker.regional["ko"]);

$(".datepicker").datepicker({
	showOn: "both",
	buttonImageOnly: true,
	buttonImage: "../img/sub/calender@2x.png",
	changeMonth: true, // True if month can be selected directly, false if only prev/next
	changeYear: true, // True if year can be selected directly, false if only prev/next
	buttonText: "Calendar",
});

// 커스텁 셀렉트
(function($) {
    $.fn.customSelect = function() {
        var customSelect = this;

        customSelect.find('.cs-btn').click(function(e) {
            e.stopPropagation();
            customSelect.find('.cs-list').slideToggle('fast');
        });

        customSelect.find('.value').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            var valueText = $(this).text();
            customSelect.find('.dp-txt').text(valueText);
            customSelect.find('.cs-list').slideUp('fast');
        });

        $(document).click(function() {
            customSelect.find('.cs-list').slideUp('fast');
        });
    };
})(jQuery);

// 툴팁
(function($) {
    $.fn.tooltipPlugin = function(options) {
        // 기본 옵션
        var settings = $.extend({
            activeClass: 'active',
        }, options);

        // 플러그인 기능 정의
        function toggleTooltip(e) {
            e.preventDefault();
            var $tooltip = $(this).closest('.tooltip');
            $('.tooltip').not($tooltip).removeClass(settings.activeClass); // 다른 모든 tooltips 비활성화
            $tooltip.toggleClass(settings.activeClass);
        }

        // 플러그인 적용
        this.each(function() {
            var $this = $(this);
            $this.find('.tt-a').off('click').on('click', toggleTooltip);
        });

        // 문서 외부 클릭 시 Tooltip 비활성화
        $(document).click(function(e) {
            if (!$(e.target).closest('.tooltip').length) {
                $('.tooltip').removeClass(settings.activeClass);
            }
        });

        return this; // 체이닝을 위해 this 반환
    };
}(jQuery));

// 팝업 시트 열기 함수
function openPopSheet(sheetId) {
	$('.pop-sheet[data-pop-sheet="' + sheetId + '"]').fadeIn().addClass('active');
}

// 팝업 시트 열기 함수
function closePopSheet(sheetId) {
	$('.pop-sheet[data-pop-sheet="' + sheetId + '"]').fadeOut().removeClass('active');
}

// 언어 선택
function initializeLanguageSelector() {
    // lang-btn 클릭 시 lang-list 슬라이드 다운/업
    $('.lang-btn').on('click', function(e) {
        e.stopPropagation(); // 클릭 이벤트가 부모 요소로 전파되지 않도록 방지
        $('.lang-list').slideToggle(200);
    });

    // btn-select-lang 클릭 시 선택된 언어로 lang-btn 텍스트 변경 및 lang-list 슬라이드 업
    $('.btn-select-lang').on('click', function(e) {
        e.preventDefault(); // 기본 링크 동작 방지
        var selectedLang = $(this).text(); // 선택된 언어 텍스트 값
        $('.lang-btn .txt').text(selectedLang); // lang-btn 텍스트 변경
        $('.lang-list').slideUp(200); // lang-list 슬라이드 업
    });

    // lang 영역 밖을 클릭하면 lang-list 슬라이드 업
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.lang').length) {
            $('.lang-list').slideUp(200);
        }
    });
}
