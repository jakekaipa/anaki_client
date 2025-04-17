@php
    $default = get_default_language_code();
@endphp
@extends('user.layouts.master')




@section('content')
    <div class="content kyc">
        <h2 class="page-tit">{{ __('KYC Verification') }}</h2>

        <div class="container-row">
            <div class="sub-box">
                <strong class="cb-tit">{{ __('본인 확인 증명') }}</strong>

                <div class="kyc-guide" id="kyc_form">
                    @if (auth()->user()->kyc_verified == global_const()::APPROVED)
                        <div class="approved text--success kyc-text">{{ __('Your KYC information is verified') }}
                        </div>
                    @else
                        <p class="kyc-txt1">{{ __('유효한 KYC 정보를 제출하십시오') }}</p>
                        <div class="kyc-img"><img src="{{ asset('/public/pub') }}/img/kyc-certification@2x.png"></div>
                        <p class="kyc-txt2">{{ __('주민등록증 또는 운전면허증 필요합니다') }}<br>{{ __('준비되었으면 시작을 클릭해 주세요') }}</p>
                        <div class="kyc-btn">
                            <button class="btn-default" onclick="openInput()">  <!--openInput()  onBegin() -->
                                <span class="txt">{{ __('Start') }}</span>
                            </button>
                        </div>
                    @endif
                </div>

                <div id="inputList">
                    <table>
                        <tr>
                            <td>
                                <label for="name">{{ __('name') }}</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="name" placeholder="Enter your name">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="birthday">{{ __('birthday') }}</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="birthday" placeholder="YYYY-MM-DD" maxlength='10'>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="phone_number">{{ __('Phone Number') }}</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="phone_number" placeholder="01012345678">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label for="email">{{ __('email') }}</label>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="text" id="email" placeholder="example@domain.com">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <input type="button" value="{{ __('submit') }}" onclick="onBegin()">
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="kycContainer" class="row mb-30-none justify-content-center" style="height:100%">
                    {{-- <div class="col-lg-12 form-group">
                    <div id="kyc_status"></div>
                </div> --}}

                    <div id="kyc" class="row mb-30-none justify-content-center"
                        style="display:none; width:100%; height:100%;">
                        <iframe id="kyc_iframe" style="background-color:white width:100%; height:100%;"
                            allow="camera"></iframe>
                    </div>

                    <div id="kyc_resultPanel" style="display: none;">
                        <div id="kyc_result" style="display: none;">
                        </div>

                        <form action="{{ setRoute('user.authorize.kyc.submit') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="">
                                <button type="submit" id="submitButton" style="display: none"
                                    class="btn-default">{{ __('submit') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<!-- 
        <form id="kycInfoUpdate" method="POST" action="{{ setRoute('user.profile.kycinfo.update') }}">
            @csrf
            <input type="hidden" name="realname" id="realname">
            <input type="hidden" name="mobile" id="mobile">
            <input type="hidden" value="0" name="index"  />
            <input type="hidden" value="" name="accountTag" id="accountTag" />
            <input type="hidden" value="" name="bankName" id="bankName" />
            <input type="hidden" value="" name="accountNumber" id="accountNumber" />
            <input type="hidden" value= "" name="accountName" id="accountName" />
        </form> -->

<style>
   #inputList {
    display: none;
    max-width: 400px;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 20px;
}

table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
}

td {
    padding: 0;
}

label {
    font-family: "Arial", sans-serif;
    font-size: 14px;
    color: #444;
    font-weight: 600;
    margin-bottom: 3px;
    display: block;
}

input[type="text"] {
    width: 100%; 
    padding: 15px; 
    border: 1px solid #ddd;
    border-radius: 6px;
    box-sizing: border-box;
    font-size: 16px; 
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    outline: none;
    background-color: #f9f9f9;
}

input[type="text"]:focus, input[type="date"]:focus {
    border-color: #007bff;
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.3);
}

input[type="button"] {
    width: 100%;
    padding: 12px;
    background-color: #21B8A1;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.3s ease;
    margin-top: 20px;
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
}

input[type="button"]:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

input[type="button"]:active {
    background-color: #004085;
    transform: translateY(0);
}

</style>

@endsection

    @push('script')
        <script>
            let KYC_TARGET_ORIGIN = 'https://kyc.useb.co.kr';
            let KYC_URL = 'https://kyc.useb.co.kr/auth';

            window.addEventListener('message', (e) => {
                // console.log('alcherakyc response', e.data); // base64 encoded된 JSON 메시지이므로 decoded해야 함
                // console.log('origin :', e.origin);
                try {
                    let decodedData;
                    try {
                        decodedData = decodeURIComponent(atob(e.data));
                    } catch (error) {
                        // console.log('Decoding failed, treating data as already decoded');
                        decodedData = e.data;
                    }
                    // console.log('decoded', decodedData);
                    // const json = JSON.parse(decodedData);
                    // console.log('json', json);

                    let json;
                    if (typeof decodedData === 'string') {
                        try {
                            json = JSON.parse(decodedData);
                        } catch (error) {
                            // console.log('JSON parsing failed, treating data as already parsed');
                            json = decodedData;
                        }
                    } else {
                        json = decodedData;
                    }
                    // console.log('json', json);
                    //return false;
                    // endKYC();

                    //let json2 = _.cloneDeep(json);
                    let json2 = json;
                    if (json2 && json2.review_result && json2.review_result.id_card) {
                        const review_result = json2 && json2.review_result;

                        if (review_result.id_card) {
                            const id_card = review_result.id_card;
                            if (id_card.id_card_image) {
                                id_card.id_card_image =
                                    id_card.id_card_image.substring(0, 20) + '...생략...';
                            }
                            if (id_card.id_card_origin) {
                                id_card.id_card_origin =
                                    id_card.id_card_origin.substring(0, 20) + '...생략...';
                            }
                            if (id_card.id_crop_image) {
                                id_card.id_crop_image =
                                    id_card.id_crop_image.substring(0, 20) + '...생략...';
                            }
                        }

                        if (review_result.face_check) {
                            const face_check = review_result.face_check;
                            if (face_check.selfie_image) {
                                face_check.selfie_image =
                                    face_check.selfie_image.substring(0, 20) + '...생략...';
                            }
                        }
                    }
                    if (json2 && json2.attachment) {
                        const attachment = json2.attachment;
                        for (const key in attachment) {
                            if (attachment[key].id) {
                                attachment[key].value =
                                    attachment[key].value.substring(0, 20) + '...생략...';
                            }
                        }
                    }

                    const str = JSON.stringify(json2, undefined, 4);
                    const strHighlight = syntaxHighlight(str);
                    const review_result = json.review_result;
                    
                    // API 인증 데이터 
                    const formData = {
                        result_type:    review_result.result_type,
                        accountTag:     review_result.account.finance_company,
                        accountName:    review_result.account.account_holder,
                        accountNumber:  review_result.account.account_number,
                        bankName:       review_result.account.finance_company,
                        realname:       review_result.name,
                        mobile:         review_result.phone_number,
                        index:          0,
                        datas:          json
                    };
                    
                    // 받은 데이터 전달 
                    $.ajax({
                        url: "{{ setRoute('user.profile.kycinfo.update') }}",
                        method: 'POST', 
                        data: {
                            ...formData,
                            _token: '{{ csrf_token() }}' 
                        },
                        success: function(response) {
                            alert(response.message);
                             // 페이지 이동
                             if(response.success === true){
                                window.location = '/user/dashboard?' + new Date().getTime();
                             } else {
                                window.location = 'user/authorize/kyc?' + new Date().getTime();
                             }
                        },
                        error: function(xhr) {
                           alert("{{ __('Something went wrong. Please try again.') }}");
                        }
                    });
                    
                   
                } catch (error) {
                    console.log('wrong data', error);
                }
            });

            function syntaxHighlight(json) {
                json = json
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                return json.replace(
                    /("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g,
                    function(match) {
                        let cls = 'number';
                        if (/^"/.test(match)) {
                            if (/:$/.test(match)) {
                                cls = 'key';
                            } else {
                                cls = 'string';
                            }
                        } else if (/true|false/.test(match)) {
                            cls = 'boolean';
                        } else if (/null/.test(match)) {
                            cls = 'null';
                        }
                        return '<span class="' + cls + '">' + match + '</span>';
                    }
                );
            }

            async function signIn(params) {
                const {
                    customer_id,
                    username,
                    password
                } = params;
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                
                const res = await fetch('/user/profile/getKYCToken', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        customer_id: Number(customer_id),
                        username,
                        password,
                    }),
                });
                return await res.json();
            }

            function isUseBDomain() {
                const USEB_DOMAIN = 'useb.co.kr';
                return window.location.hostname.includes(USEB_DOMAIN);
            }

            function getSignInURL() {
                if (isUseBDomain()) {
                    const delimiter = 'https://kyc';
                    const tmp = KYC_TARGET_ORIGIN.split(delimiter);
                    return delimiter + '-api' + tmp[1];
                } else {
                    // POST https://kyc-api.useb.co.kr/sign-in API는 운영계에서 CORS가 미허용되어 있습니다.
                    // 따라서, 브라우저(클라이언트)에서 운영계 URL로 /sign-in 호출시 CORS 이슈가 발생되며,
                    // 고객사 서버에서 운영계 URL(https://kyc-api.useb.co.kr/sign-in)로 API 호출 후
                    // 응답받은 token을 access_token으로 넣어 연동해야합니다.
                    // 예제에서는 CORS가 허용 되어있는 계발계 URL로 임시 호출하도록 되어있습니다.
                    return 'https://kyc-api-dev.useb.co.kr';
                }
            }

            function initKYC() {
                document.getElementById('kyc').style.display = 'none';
            }

            function startKYC() {
                document.getElementById('kyc_form').style.display = 'none';
                const kycElement = document.getElementById('kyc');
                kycElement.style.display = 'flex';
                kycElement.style.height = '100%';
                document.getElementById('kyc_iframe').style.height = '100%';
            }

            function endKYC() {
                document.getElementById('kyc_form').style.display = 'none';
                document.getElementById('kyc').style.display = 'none';
                document.getElementById('kyc_resultPanel').style.display = 'flex';

                // console.log("endKYC");
            }


            function updateKYCStatus(msg) {
                alert(msg);
                // const div = document.getElementById('kyc_status');
                // div.innerHTML = msg;
            }

            //인풋박스 보이기
            function openInput(){
                $('.cb-tit').css('display','none');
                document.getElementById('kyc_form').style.display = 'none';
                document.getElementById('inputList').style.display = 'block';
            }

            // 생일 하이폰 추가
            const birthdayInput = document.getElementById('birthday');
            birthdayInput.addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 8) {
                    value = value.slice(0, 8);
                }

                let formattedValue = '';
                if (value.length <= 4) {
                    formattedValue = value; // 1111
                } else if (value.length <= 6) {
                    formattedValue = `${value.slice(0, 4)}-${value.slice(4)}`; // 1111-11
                } else if (value.length <= 8) {
                    formattedValue = `${value.slice(0, 4)}-${value.slice(4, 6)}-${value.slice(6)}`; // 1111-11-11
                }
                e.target.value = formattedValue;
            });
            
            // 전화번호 하이폰 제외
            const phoneNumberInput = document.getElementById('phone_number');
            phoneNumberInput.addEventListener('input', function (e) {
                // 입력 값에서 하이픈(-) 제거
                this.value = this.value.replace(/-/g, '');
            });

            async function onBegin() {
            //   기존키로 변경
              const name = document.getElementById('name').value.trim();
              const birthday = document.getElementById('birthday').value;
              const phoneNumber =  document.getElementById('phone_number').value.replace(/\D/g, '');
              const email =   document.getElementById('email').value;
              const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
              
            //   console.log('name='+name+'/////'+'birthday='+birthday+'/////'+'phoneNumber='+phoneNumber+'/////'+'email='+email); 
            //   return false;

              if(!name){
                alert('이름 입력해주세요');
                return false;
              } else if(!birthday){
                alert('생일을 입력해주세요');
                return false;
              } else if(!phoneNumber){
                alert('휴대폰번호를 입력해주세요');
                return false;
              } else if(!email){
                alert('이메일을 입력해주세요');
                return false;
              } else if(!emailPattern.test(email)){
                alert('유효한 이메일을 입력해주세요');
                return false;
              }
            
              // 입력 내용 제거
              document.getElementById('inputList').style.display = 'none';
            
              const kycIframe = document.getElementById('kyc_iframe');
              const params = {
                "access_token":  await getToken(),
                "name": name, 
                "birthday": birthday, 
                "phone_number": phoneNumber, 
                "email": email
               };

              kycIframe.onload = async function () {
                    try {
                        let encodedParams = btoa(encodeURIComponent(JSON.stringify(params)));
                        kycIframe.contentWindow.postMessage(encodedParams, KYC_TARGET_ORIGIN);
                        startKYC(); 

                        kycContainer.style.height = '800px';
                    } catch (error) {
                        console.error('Error in iframe onload:', error);
                    } finally {
                     
                    }
                };

                kycIframe.src = KYC_URL;
            }

            document.querySelectorAll('input').forEach((element) => {
                element.onkeyup = (e) => {
                    const target = e.srcElement || e.target;
                    if (!e || e.key === 'Tab' || e.key === 'Shift' || e.key === 'Process') {
                        return;
                    }
                    if (!target || !target.attributes['maxlength']) {
                        return;
                    }
                    const maxLength = parseInt(target.attributes['maxlength'].value, 10);
                    const myLength = target.value.length;

                    if (myLength > maxLength) {
                        target.value = parseInt((target.value + '').substr(0, maxLength), 10);
                    }

                    if (myLength >= maxLength) {
                        let next = target;
                        while ((next = next.nextElementSibling)) {
                            if (next == null) break;
                            if (next.tagName.toLowerCase() === 'input') {
                                next.focus();
                                break;
                            }
                        }
                    }
                    // Move to previous field if empty (user pressed backspace)
                    else if (myLength === 0) {
                        let previous = target;
                        while ((previous = previous.previousElementSibling)) {
                            if (previous === null) break;
                            if (previous.tagName.toLowerCase() === 'input') {
                                previous.focus();
                                break;
                            }
                        }
                    }
                };
            })
            
            // KYC 토큰 가져오기
            async function getToken() {
                try {
                    const res = await fetch('/user/profile/getKYCToken', {
                        method: 'GET',  
                    });

                    if (res.ok) {
                        const data = await res.json(); 
                        return data.token; 
                    } else {
                        console.error('Request failed with status:', res.status);
                        return null;  
                    }
                } catch (error) {
                    console.error('Error:', error);
                    return null; 
                }
            }

        
            function updateKYCResult(data, json) {
                const imageConverter = function(str) {
                    return 'data:image/jpeg;base64,' + str;
                };

                const kycResult = document.getElementById('kyc_result');

                kycResult.innerHTML = '';

                const title1 = document.createElement('h3');
                title1.innerHTML = '<h3 class="custom--headline">최종 결과</h3>';

                const result1 = document.createElement('div');
                result1.className = 'syntaxHighlight bright';
                result1.style.textAlign = 'center';

                console.dir(json);

                const detail = json.review_result;
                let content = '';


                if (detail && detail.id_card.verified) {
                    document.getElementById('accountTag').value = detail.account.finance_company;
                    document.getElementById('accountName').value = detail.account.account_holder;
                    document.getElementById('accountNumber').value = detail.account.account_number;
                    document.getElementById('bankName').value = detail.account.finance_company;

                    document.getElementById('realname').value = detail.name;
                    document.getElementById('mobile').value = detail.phone_number;
                    document.getElementById('kycInfoUpdate').submit();
                }

                if (detail) {
                    let result_type_txt = 'N/A';
                    if (detail.result_type === 1) {
                        result_type_txt = "<span style='color:blue'>자동승인</span>";
                    } else if (detail.result_type === 2) {
                        result_type_txt = "<span style='color:red'>자동거부</span>";
                    } else if (detail.result_type === 5) {
                        result_type_txt = "<span style='color:orange'>수동심사대상</span>";
                    } else {
                        result_type_txt = 'INVALID_TYPE';
                    }
                    title1.innerHTML +=
                        '- 인증 결과 : ' +
                        (json.result === 'success' ?
                            "<span style='color:blue'>성공</span>" :
                            "<span style='color:red'>실패</span>") +
                        ' </br>';
                    title1.innerHTML += '- 종합 판정 결과 : ' + result_type_txt + ' </br>';

                    if (detail.module.id_card_ocr) {
                        content =
                            "<h5><span style='color:blue'>■ 정상</span> | <span style='color:red'>■ 거부사유</span> | <span style='color:orange'>■ 수동심사사유</span> | <span style='color:purple'>■ 참고사항</span></h5>";
                        content += "<h4 class='subTitle'>신분증 인증 결과</h4>";
                        content +=
                            '<br/> - 정부기관 대조 결과 : ' +
                            (detail.id_card && !detail.module.id_card_verification ?
                                'N/A' :
                                detail.id_card.verified ?
                                "<span style='color:blue'>성공</span>" :
                                "<span style='color:red'>실패</span>");

                        if (detail.id_card.modified !== undefined) {
                            content +=
                                '<br/> - 정보수정여부 : ' +
                                (detail.id_card.modified === false ?
                                    "<span style='color:blue'>NO</span>" :
                                    "<span style='color:orange'>YES</span>");
                        }

                        if (detail.id_card.is_uploaded !== undefined) {
                            content +=
                                '<br/> - 신분증 제출방식 : ' +
                                (detail.id_card.is_uploaded === false ?
                                    "<span style='color:blue'>카메라 촬영</span>" :
                                    "<span style='color:purple'>파일 업로드</span>");
                        }

                        if (detail.id_card.original_ocr_data) {
                            try {
                                const original_ocr_data = JSON.parse(
                                    detail.id_card.original_ocr_data
                                );
                                if (original_ocr_data.truth) {
                                    content +=
                                        '<br/> - 신분증 사본 판별 결과 : ' +
                                        (original_ocr_data.truth === 'REAL' ?
                                            "<span style='color:blue'>REAL</span>" :
                                            "<span style='color:purple'>FAKE</span>");
                                    content +=
                                        '<br/> - 신분증 사본 판별 Confidence : ' +
                                        (original_ocr_data.truth === 'REAL' ?
                                            "<span style='color:blue'>" +
                                            original_ocr_data.truthConfidence +
                                            '</span>' :
                                            "<span style='color:purple'>" +
                                            original_ocr_data.truthConfidence +
                                            '</span>');
                                }
                            } catch (e) {
                                console.error('original_ocr_data JSON parse error : ' + e);
                            }
                        }

                        if (detail.id_card.id_card_image) {
                            content += '<br/>';
                            content +=
                                "<br/> - 신분증 마스킹 사진<br/>&nbsp;&nbsp;&nbsp;<img style='max-height:200px;' src='" +
                                imageConverter(detail.id_card.id_card_image) +
                                "' /></b>";
                        }

                        if (detail.id_card.id_card_origin) {
                            content +=
                                "<br/> - 신분증 원본 사진<br/>&nbsp;&nbsp;&nbsp;<img style='max-height:200px;' src='" +
                                imageConverter(detail.id_card.id_card_origin) +
                                "' /></b>";
                        }
                    }

                    if (detail.module.face_authentication) {
                        content += '<br/>';
                        content +=
                            "<h4 class='subTitle'>신분증 얼굴 사진 VS 셀피 사진 유사도</h4>";
                        content +=
                            '<br/> - 유사도 측정 결과 : ' +
                            (detail.face_check ?
                                detail.face_check.is_same_person ?
                                "<span style='color:blue'>높음</span>" :
                                "<span style='color:orange'>낮음</span>" :
                                'N/A');
                        if (detail.face_check) {
                            content +=
                                "<br/> - 신분증 얼굴 사진<br/>&nbsp;&nbsp;&nbsp;<img style='max-height:100px;' src='" +
                                imageConverter(detail.id_card.id_crop_image) +
                                "' />";
                            content +=
                                "<br/> - 셀피 촬영 사진<br/>&nbsp;&nbsp;&nbsp;<img style='max-height:100px;' src='" +
                                imageConverter(detail.face_check.selfie_image) +
                                "' />";
                        }
                    }

                    if (detail.module.liveness) {
                        content += '<br/>';
                        content += "<h4 class='subTitle'>셀피 사진 진위확인</h4>";
                        content +=
                            '<br/> - 셀피(얼굴) 사진 진위확인(라이브니스) 결과 : ' +
                            (detail.face_check ?
                                detail.face_check.is_live ?
                                "<span style='color:blue'>성공</span>" :
                                "<span style='color:red'>실패</span>" :
                                'N/A');
                    }

                    if (detail.module.account_verification) {
                        content += '<br/>';
                        content += "<h4 class='subTitle'>1원 계좌 인증</h4>";
                        content +=
                            '<br/> - 1원 계좌 인증 결과 : ' +
                            (detail.account ?
                                detail.account.verified ?
                                "<span style='color:blue'>성공</span>" :
                                "<span style='color:red'>실패</span>" :
                                'N/A');
                        if (detail.account) {
                            content +=
                                '<br/> - 예금주명 : ' +
                                (detail.account.account_holder ?
                                    detail.account.account_holder :
                                    'N/A');
                            content +=
                                '<br/> - 수정된 예금주명(수정한 경우만) : ' +
                                (detail.account.mod_account_holder ?
                                    "<span style='color:orange'>" +
                                    detail.account.mod_account_holder +
                                    '</span>' :
                                    'N/A');
                            content +=
                                '<br/> - 금융사명 : ' +
                                (detail.account.finance_company ?
                                    detail.account.finance_company :
                                    'N/A');
                            content +=
                                '<br/> - 금융사코드 : ' +
                                (detail.account.finance_code ? detail.account.finance_code : 'N/A');
                            content +=
                                '<br/> - 계좌번호 : ' +
                                (detail.account.account_number ?
                                    detail.account.account_number :
                                    'N/A');
                        }
                    }
                }

                result1.innerHTML = content;
                kycResult.appendChild(title1);
                kycResult.appendChild(result1);

                //const title2 = document.createElement('h3');
                //title2.innerHTML = '<h3 class="custom--headline">PostMessage 상세</h3>';

                // const result2 = document.createElement('pre');
                // result2.className = 'syntaxHighlight bright';
                // result2.innerHTML = data;
                // kycResult.appendChild(title2);
                // kycResult.appendChild(result2);
            }

        </script>
    @endpush
