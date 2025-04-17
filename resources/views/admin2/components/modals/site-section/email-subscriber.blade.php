<div id="subscriber-email-send" class="mfp-hide large">
    <div class="modal-data">
        <div class="modal-header px-0">
            <h5 class="modal-title">{{ __("Send Email") }}</h5>
        </div>
        <div class="modal-form-data">
            <form class="card-form" action="{{ setRoute('admin.subscriber.email.send') }}" method="post">
                @csrf
                <div class="row mb-10-none">
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.form.input',[
                            'label'         => 'Subject*',
                            'name'          => 'subject',
                            'value'         => old('subject'),
                            'placeholder'   => "Write Here...",
                        ])
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.form.input-text-rich',[
                            'label'         => 'Details*',
                            'name'          => 'message',
                            'value'         => old('message'),
                            'placeholder'   => "Write Here...",
                        ])
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn',[
                            'class'         => "w-100 btn-loading",
                            'permission'    => "admin.users.email.users.send",
                            'text'          => "Send Email",
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

