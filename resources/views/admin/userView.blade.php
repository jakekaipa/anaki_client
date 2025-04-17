@extends('admin.layouts.mainLayout')
@section("title")
User View
@endsection
@section('style')
<style>
    body {
        background-color: #f1f4f8;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }
    .card-header {
        background-color: #007bff;
        color: white;
        font-weight: bold;
        font-size: 1.25rem;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        padding: 1.25rem;
    }
    .card-body {
        padding: 2rem;
    }
    .card-footer {
        background-color: #f8f9fa;
        border-bottom-left-radius: 15px;
        border-bottom-right-radius: 15px;
    }
    .btn-back, .btn-change {
        background-color: #28a745;
        color: white;
        border-radius: 5px;
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    .btn-back:hover, .btn-change:hover {
        background-color: #218838;
    }
    .card-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .card-text {
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }
    .icon {
        color: #007bff;
    }
    .img-profile {
        width: 180px;
        height: 180px;
        border-radius: 50%;
        border: 4px solid #007bff;
    }
    .profile-info {
        padding-left: 1.5rem;
    }
    .profile-info p {
        margin-bottom: 1rem;
    }

    .password-wrapper {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
    }

    /* 비밀번호 입력창과 버튼을 세로로 배치 */
    .password-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 20px;
        width: 50%; /* 부모 컨테이너 너비를 50%로 설정 */
    }

    /* 비밀번호 입력 필드 크기 조정 */
    .password-wrapper input {
        padding: 10px;
        font-size: 1rem;
        width: 100%;  /* 인풋 너비를 100%로 설정 */
        max-width: 100%;  /* 최대 너비도 100%로 설정 */
        border-radius: 5px;
        border: 1px solid #ced4da;
    }

    .password-wrapper button:hover {
        background-color: #0056b3;
    }

    /* 비밀번호 입력란 위에 label 추가 스타일 */
    .password-label {
        font-size: 1.1rem;
        font-weight: bold;
        margin-bottom: 5px;
    }

    /* 버튼을 가로로 배치 */
    .button-group {
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .btn-change {
        background-color: #007bff;
    }
    .verify-button {
      background: linear-gradient(45deg, #32cd32, #228b22);
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease, transform 0.2s ease-in-out;
      position: relative;
      overflow: hidden;
      margin-top:10px;
    }

    .verify-button:hover {
      background: linear-gradient(45deg, #228b22, #32cd32);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px) scale(1.05);
    }

    .verify-button-no {
      background: linear-gradient(45deg, #ff6347, #e60000);
      color: white;
      border: none;
      border-radius: 20px;
      padding: 12px 25px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease, transform 0.2s ease-in-out;
      position: relative;
      overflow: hidden;
      margin-top:10px;
    }

    .verify-button-no:hover {
      background: linear-gradient(45deg, #e60000, #ff6347);
      box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
      transform: translateY(-2px) scale(1.05);
    }
</style>
@endsection

@section('content')
<div class="container mt-5">
    <h2 class="mb-4 text-center">유저 정보 상세보기</h2>

    <!-- 카드 형태로 사용자 정보 표시 -->
    <div class="card">
        <div class="card-header">
            <i class="fas fa-user-circle"></i> {{ $userInfo->username }} ({{ $userInfo->realname }})님의 정보
        </div>
    <form id='submitForm' method='post'>
        @csrf
        <div class="card-body">
            <input type='hidden' name='userId' value="{{ urlSafeEncrypt($userInfo->id) }} ">
            <div class="row">
                <!-- 첫 번째 열 -->
                <div class="col-md-6 profile-info">
                    <p class="card-text">
                        <i class="fas fa-envelope icon"></i> 
                        <strong>Email</strong>
                        <span class="d-block">{{ $userInfo->email }}</span>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-calendar-alt icon"></i> 
                        <strong>휴대폰 번호(KYC 미인증시 공백)</strong> 
                        <span class="d-block">{{ $userInfo->mobile }}</span>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-wallet icon"></i>
                        <strong>지갑주소</strong> 
                        <span class="d-block">{{ $userInfo->walletAddress }}</span>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-usd icon"></i>
                        <strong>보유USDT</strong> 
                        <span class="d-block">{{ $userInfo->usdtBalance }}</span>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-usd icon"></i>
                        <strong>펜딩USDT</strong> 
                        <span class="d-block">{{ $userInfo->usdtPending }}</span>
                    </p>
                    <div class="password-wrapper">
                        <div>
                            <label for="newPassword" class="password-label">비밀번호(변경 시 입력)</label>
                            <input type="password" name='password' id="newPassword" maxlength="10" placeholder="비밀번호">
                        </div>
                    </div>  
                </div>

                <!-- 두 번째 열 -->
                <div class="col-md-6 profile-info">
                    <p class="card-text">
                        <i class="fas fa-check-circle icon"></i>
                        <strong>KYC인증</strong>
                        <br>
                        <button type="button" class="verify-button{{ ( $userInfo->kyc_verified == 0)? '-no':''  }}" >{{  $userInfo->kyc_verified == 1 ? __('kyc_verified') : __('kyc_unverified') }}</button>
                    </p>
                    <p class="card-text">
                        <i class="fas fa-check-circle icon"></i>
                        <strong>2FA 사용</strong>
                        <br>
                        <button type="button" class="verify-button{{ (!$userInfo->two_factor_secret)? '-no':''  }}" >{{  ($userInfo->two_factor_secret) ? __('Activate') : __('Disable') }}</button>
                    </p>
                    @if(isset($userInfo->getReferenceInfo) && $userInfo->getReferenceInfo->group_id == 0)
                    <p class="card-text">
                        <i class="fas fa-usd icon"></i>
                        <strong>총 레퍼럴 수수료(USDT)</strong><input type='button' class="btn btn-primary" value='내역확인' onclick="checkReferenceList('{{ urlSafeEncrypt($userInfo->getReferenceInfo->id) }}')">
                        <span class="d-block">{{ $userInfo->getReferenceInfo->referral_benefit_total }}</span>
                    </p>
                    @endif
                    <p class="card-text">
                        <i class="fas fa-user-tag icon"></i> 
                        <strong>상태</strong>
                        <div class="d-block">
                            <select name="status" class="form-control form-control-sm" style="width: 35%;">
                                <option {{ ($userInfo->status == 1) ? 'selected' : '' }} value="1">사용</option>
                                <option {{ ($userInfo->status == 0) ? 'selected' : '' }} value="0">정지</option>
                            </select>
                        </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <div class="button-group">
                <button type='button' class="btn btn-change btn-lg" onclick="changeInfo()">변경하기</button>
                <a href="javascript:history.go(-1);" class="btn btn-change btn-lg" style="margin-left: 20px;"> 뒤로가기</a>
            </div>
        </div>
    </form>
    </div>
</div>

@if(session('success'))
    <script>
        alert("{{ session('success') }}");
    </script>
@endif

@endsection

@section('script')
<script>
    function changeInfo() {
        let result = confirm("수정 하시겠습니까?");
        if (result) { 
            var newPassword = document.getElementById('newPassword').value;
            if(newPassword.length > 1 ){
                if (newPassword.length < 6) {
                    alert("비밀번호는 최소 6자리 이상이어야 합니다.");
                    return;
                }
            }
            $('#submitForm').attr('action', '/admin/manage-user/user-update');
            $('#submitForm').submit();
        } 
    }

    // 레퍼럴 수수료 내역 새창
    function checkReferenceList(id) {
        var width = 800;  // 새 창의 가로 크기
        var height = 600; // 새 창의 세로 크기
        var left = (window.innerWidth / 2) - (width / 2);  // 화면 중앙에서 새 창의 좌측 위치
        var top = (window.innerHeight / 2) - (height / 2);  // 화면 중앙에서 새 창의 상단 위치

        var windowFeatures = `width=${width}, height=${height}, left=${left}, top=${top}, resizable=yes, scrollbars=yes`;
        window.open("/admin/manage-user/referral-list/" + id, "_blank",windowFeatures);
     
    }
</script>
@endsection
