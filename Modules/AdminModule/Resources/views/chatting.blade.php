@section('title', translate('Chatting_List'))

@extends('adminmodule::layouts.master')

@push('css_or_js')
    <link rel="stylesheet" href="{{ asset('public/landing-page/assets/css/owl.min.css') }}"/>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="mb-3">
            <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
                <h2 class="fs-22 text-capitalize">
                    <img src="{{ asset('/public/assets/admin-module/img/chat-logo.png') }}" alt="">
                    {{ translate('Chatting List') }}
                </h2>
                @if (businessConfig(key: 'chatting_setup_status')?->value)
                    <button type="button" class="btn btn-outline-primary light-border radius-35"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#filter-offcanvas">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <g id="comments-question 1" clip-path="url(#clip0_4766_3113)">
                                <path id="Vector"
                                      d="M10.242 1.75732C9.01333 0.527989 7.30933 -0.111345 5.566 0.015322C2.54867 0.228655 0 3.05932 0 6.19732V9.55599C0 10.9033 1.09467 12 2.44067 12H5.42533C8.88333 12 11.7647 9.55532 11.9847 6.43399C12.108 4.69199 11.4727 2.98732 10.2427 1.75666L10.242 1.75732ZM6 9.66666C5.54 9.66666 5.16667 9.29332 5.16667 8.83332C5.16667 8.37332 5.54 7.99999 6 7.99999C6.46 7.99999 6.83333 8.37332 6.83333 8.83332C6.83333 9.29332 6.46 9.66666 6 9.66666ZM6.96467 6.41932C6.66667 6.58332 6.66667 6.63132 6.66667 6.66666C6.66667 7.03532 6.368 7.33332 6 7.33332C5.632 7.33332 5.33333 7.03532 5.33333 6.66666C5.33333 5.79466 6.02533 5.41399 6.32067 5.25132C6.514 5.14532 6.71733 4.89466 6.65533 4.54066C6.60933 4.27932 6.38733 4.05732 6.12667 4.01199C5.92267 3.97466 5.72533 4.02666 5.572 4.15599C5.42 4.28266 5.33333 4.46932 5.33333 4.66732C5.33333 5.03599 5.03467 5.33399 4.66667 5.33399C4.29867 5.33399 4 5.03599 4 4.66732C4 4.07466 4.26067 3.51599 4.71467 3.13466C5.16867 2.75332 5.766 2.59199 6.35667 2.69932C7.16267 2.83999 7.826 3.50266 7.968 4.31066C8.11667 5.15866 7.71333 6.00599 6.964 6.41999L6.96467 6.41932ZM16 10.6667V14C16 15.1047 15.1047 16 14 16H10.6667C8.69667 16 6.97667 14.9233 6.05333 13.3307L6.08 13.304C9.938 13.0093 13.06 10.1413 13.3147 6.52799C13.326 6.37199 13.3213 6.21732 13.3227 6.06199L13.3313 6.05332C14.924 6.97666 16 8.69666 16 10.6667Z"
                                      fill="currentColor"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_4766_3113">
                                    <rect width="16" height="16" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>


                        <span class="title-color"> {{ translate('View Save Answer') }}</span>
                    </button>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-lg-4 chatSel">
                <div class="card card-body px-0 h-100 max-h-100vh-220">
                    <div class="inbox_people">
                        <form class="search-form mb-4 px-20" id="chat-search-form">
                            <div class="input-group search-form__input_group">
                                <input type="text"
                                       class="theme-input-style search-form__input fw-semibold chatting-drivers-list-search"
                                       value="" name="search" id="search" placeholder="Search driver">
                                <span class="search-form__icon pe-0">
                                    <i class="bi bi-search"></i>
                                </span>
                            </div>
                        </form>
                        <h5 class="mb-3 px-20">{{ translate('Drivers') }}</h5>
                    </div>
                    <div id="chatting-drivers-list">
                        @include('adminmodule::partials.chatting._search-drivers')
                    </div>
                </div>
            </div>

            <section class="col-xl-9 col-lg-8 mt-4 mt-lg-0">

                {{-- Empty State --}}
                <div class="card card-body card-chat justify-content-center border-0 empty-chat-state" id="">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="d-flex flex-column align-items-center gap-20">
                            <img width="48" src="{{ asset('/public/assets/admin-module/img/svg/driver-man.svg') }}"
                                 alt="">
                            <p class="fs-16">{{ translate('Select a driver form list') }}</p>
                        </div>
                    </div>

                </div>

                {{-- Typing state ends --}}
                <div id="chattingConversation">
                </div>
            </section>
        </div>

        <span id="image-url" data-url=""></span>
        <span id="chatting-post-url" data-url="{{ route('admin.send-message-to-driver') }}"></span>
        <meta name="csrf-token" content="{{ csrf_token() }}">

    </div>
    {{-- Saved answer offcanvas --}}
    @if (businessConfig(key: 'chatting_setup_status')?->value)
        <div class="offcanvas offcanvas-end" id="filter-offcanvas">
            <div class="offcanvas-header">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                <h4 class="offcanvas-title flex-grow-1 text-center">
                    {{ translate('Saved Answer') }}
                </h4>
            </div>
            <div class="offcanvas-body scrollbar-thin">
                <div class="input-group search-form__input_group px-3">
                    <input type="text" class="theme-input-style search-form__input fw-semibold" value=""
                           name="search" id="searchSavedReply" placeholder="Search by topics">
                    <span class="search-form__icon pe-0">
                            <i class="bi bi-search"></i>
                        </span>
                </div>
                <div id="savedTopicAnswer" class="mt-2">
                    @include('adminmodule::partials.chatting._saved-answer')
                </div>
            </div>
        </div>
    @endif
    {{-- Saved answer offcanvas ends --}}


    <span id="get-file-icon" data-default-icon="{{ asset('public/assets/admin-module/img/default-icon.png') }}"
          data-word-icon="{{ asset('public/assets/admin-module/img/default-icon.png') }}"></span>
@endsection

@push('script')
    <script src="{{ asset('public/assets/admin-module/js/chatting/chatting.js') }}"></script>
    <script src="{{ asset('public/assets/admin-module/js/chatting/picmo-emoji.js') }}"></script>
    <script src="{{ asset('public/landing-page/assets/js/owl.min.js') }}"></script>




    <script>
        "use strict";

        // --- copy text
        $(document).ready(function () {
            conversation();

            let selectedDriverChannelId = null;
            let value = '';

            function activeDriver() {
                if (selectedDriverChannelId) {
                    let driverConversation = $('.driver-conversation');
                    driverConversation.each(function () {
                        if ($(this).data('channel-id') == selectedDriverChannelId) {
                            $(this).addClass('active');
                        }
                    });
                }
            }


            function driverList(url, value = '') {
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: {
                        search: value,
                    },
                    success: function (response) {
                        $('#chatting-drivers-list').empty().html(response);
                        conversation();
                        activeDriver();
                        
                        $(".driver-conversation.active")[0].scrollIntoView({
                            behavior: "auto",
                            block: "nearest",
                            inline: "center",
                        });
                    }
                })
            }

            //search-drivers-list
            $('.chatting-drivers-list-search').on('keyup', function () {
                value = $(this).val().toLowerCase();
                driverList('{{ route('admin.search-drivers') }}', value);
            });
            //end-search-drivers-list
            // search-saved-answer-list
            $('#searchSavedReply').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $.ajax({
                    url: '{{ route('admin.search-saved-topic-answers') }}',
                    type: 'GET',
                    data: {
                        search: value
                    },
                    success: function (response) {
                        $('#savedTopicAnswer').empty().html(response);
                    }
                })
            });
            //end-search-saved-answer-list

            //chattingConversationShow
            let emptyState = $('.empty-chat-state');
            // ending chattingConversationShow

            // Success Callback for Chatting Conversation
            function onSuccess(response) {
                $('#chattingConversation').empty().html(response);
                driverList('{{ route('admin.search-drivers') }}', value);
                // Dynamically load scripts
                $.getScript("{{ asset('public/assets/admin-module/js/chatting/emoji.js') }}");
                $.getScript("{{ asset('public/assets/admin-module/js/chatting/select-multiple-file.js') }}");
                $.getScript(
                    "{{ asset('public/assets/admin-module/js/chatting/select-multiple-image-for-message.js') }}"
                );
                $.getScript("{{ asset('public/assets/admin-module/js/js-zip/jszip.min.js') }}");
                $.getScript("{{ asset('public/assets/admin-module/js/js-zip/FileSaver.min.js') }}");
                //tooltip
                // $('[data-bs-toggle="tooltip"]').tooltip();
                const tooltipElements = $('[data-bs-toggle="tooltip"]');
                tooltipElements.tooltip({
                    container: '.conversation', 
                    boundary: 'window',
                });
                imageSlider();
                fileUpload();
                msgBtn();
            }

            function conversation() {
                let driverConversation = $('.driver-conversation');
                driverConversation.on('click', function () {
                    $(this).addClass('active').siblings().removeClass('active');
                    let driverId = $(this).data('driver-id');
                    let conversationElement = $(this);

                    if (conversationElement.attr('data-channel-id')) {
                        let channelId = conversationElement.attr('data-channel-id');
                        selectedDriverChannelId = channelId;
                        let url = '{{ route('admin.driver-conversation', ':channelId') }}';
                        $.ajax({
                            url: url.replace(':channelId', channelId),
                            type: 'GET',
                            data: {
                                driverId: driverId,
                            },
                            success: function (response) {
                                emptyState.addClass('d-none');
                                onSuccess(response);
                                ajaxFormRenderChattingMessages();
                            }
                        });
                    } else {
                        $.ajax({
                            url: '{{ route('admin.create-channel-with-admin') }}',
                            type: 'PUT',
                            data: {
                                driverId: driverId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                let newChannelId = response.data.channel.id;
                                conversationElement.attr('data-channel-id', newChannelId);
                                selectedDriverChannelId = newChannelId;
                                let url = '{{ route('admin.driver-conversation', ':channelId') }}';
                                url = url.replace(':channelId', newChannelId);
                                $.ajax({
                                    url: url,
                                    type: 'GET',
                                    data: {
                                        driverId: driverId,
                                    },
                                    success: function (response) {
                                        // Perform actions on success
                                        emptyState.addClass('d-none');
                                        onSuccess(response);
                                        ajaxFormRenderChattingMessages();
                                    },
                                    error: function (xhr, status, error) {
                                        console.log('Error:', error);
                                    }
                                });
                            },
                            error: function (xhr, status, error) {
                                console.log('Error:', error);
                            }
                        });
                    }

                });
            }

            function ajaxFormRenderChattingMessages() {
                $(".chatting-messages-ajax-form").on("submit", function (event) {
                    event.preventDefault();

                    if (selectedImages.length > 0 || selectedFiles.length > 0) {
                        $(".circle-progress-2").show();

                        var totalUploads = selectedImages.length + selectedFiles.length;
                        var uploadedCount = 0;

                        if (uploadedCount < totalUploads) {
                            uploadedCount++;

                            $(".circle-progress-2 .progress-text .file-count").text(
                                totalUploads);
                            $(".circle-progress-2")
                                .find("#bar")
                                .attr(
                                    "stroke-dashoffset",
                                    100 - totalUploads
                                );
                        }
                    }
                    let formData = new FormData(this);
                    let channelId = $('#msgSendBtn').data('channel-id');
                    let driverId = $('#msgSendBtn').data('driver-id');
                    formData.append('channelId', channelId);
                    formData.append('driverId', driverId);
                    $.ajaxSetup({
                        headers: {
                            "X-XSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                    });
                    $.ajax({
                        type: "POST",
                        url: $("#chatting-post-url").data("url"),
                        data: formData,
                        processData: false,
                        contentType: false,
                        xhr: function () {
                            var xhr = new window.XMLHttpRequest();
                            xhr.upload.addEventListener(
                                "progress",
                                function (evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete =
                                            (evt.loaded / evt.total) * 100;
                                        $(".circle-progress-2")
                                            .find(".text")
                                            .text(
                                                `Uploading ${selectedFiles.length} files`
                                            );
                                        $(".circle-progress-2")
                                            .find("#bar")
                                            .attr(
                                                "stroke-dashoffset",
                                                100 - percentComplete
                                            );
                                        if (percentComplete == 100) {
                                            $(".circle-progress-2")
                                                .find(".text")
                                                .text(
                                                    `Uploaded ${selectedFiles.length} files`
                                                );
                                            $(".circle-progress-2").hide();
                                        }
                                    }
                                },
                                false
                            );
                            return xhr;
                        },
                        beforeSend: function () {
                            $("#msgSendBtn").attr("disabled", true);
                        },
                        success: function (response) {
                            onSuccess(response);
                            conversation();
                            ajaxFormRenderChattingMessages();
                            $("#msgInputValue").val("");
                            $(".image-array").empty();
                            $(".file-array").empty();
                            let container = document.getElementById(
                                "selected-files-container"
                            );
                            let containerImage = document.getElementById(
                                "selected-image-container"
                            );
                            container.innerHTML = "";
                            containerImage.innerHTML = "";
                            selectedFiles = [];
                            selectedImages = [];
                            msgBtn();
                        },
                        complete: function () {
                            $(".circle-progress-2").hide();
                            $('[data-toggle="tooltip"]').tooltip();
                        },
                        error: function (error) {
                            let errorData = JSON.parse(error.responseText);
                            toastr.warning(errorData.message);
                        },
                    });
                });
            }
        });
    </script>
@endpush
