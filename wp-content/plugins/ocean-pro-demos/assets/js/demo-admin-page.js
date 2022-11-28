// Document Ready
jQuery(document).ready(function ($) {

    var timer = null;
    var searchSlug = '';
    var selectedProvider = demo_api_images_set_localize.active_providers.length ? demo_api_images_set_localize.active_providers[0] : '';

    // Render API Images
    var init = function init() {
        var container = $(".demo-api-images-container")[0];

        var wrapper = document.createElement("div");
        wrapper.classList.add("demo-api-images-wrapper", "loading");

        var wrapperLoader = document.createElement("div");
        wrapperLoader.classList.add("demo-api-images-wrapper-loader");
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));

        wrapper.appendChild(wrapperLoader);

        var grid = document.createElement("div");
        grid.setAttribute("class", "demo-api-images-media-grid");

        var loadMoreButtonWrapper = demoApiImagesCreateLoadMoreButton();

        let apiUrlSearchParams = demoApiImagesBuildSearchParams(1);

        if ($('.demo-api-images-search').val() !== '' && $('.demo-api-images-search').val() !== undefined) {

            switch (selectedProvider) {
                case 'flaticon':
                    apiUrlSearchParams.set('q', $('.demo-api-images-search').val());
                    break;
                case 'freepik':
                    apiUrlSearchParams.set('term', $('.demo-api-images-search').val());
                    break;
            }

            searchSlug = $('.demo-api-images-search').val();

        } else {
            searchSlug = '';
        }

        $.ajax({
            url: demo_api_images_set_localize.api_service_url,
            method: "POST",
            data: {
                provider_id: selectedProvider,
                searchQuery: apiUrlSearchParams.toString(),
                searchSlug: searchSlug,
                action: 'get',
                plugin_id: demo_api_images_set_localize.fs_plugin_id,
                wp_site_url: demo_api_images_set_localize.wp_site_url,
                license_id: demo_api_images_set_localize.fs_license_id,
                is_registered: demo_api_images_set_localize.fs_is_registered,
                is_tracking_allowed: demo_api_images_set_localize.fs_is_tracking_allowed
            },
            success: function (data) {
                if(data.success == false) {
                    showErrorWrapper(data.msg);
                } else {
                    data.images.forEach(function (imgData) {
                        var gridItem = demoApiImagesCreateGridItemWithImage(imgData);
                        grid.appendChild(gridItem);
    
                        $('.demo-api-images-loadmore-btn').show();
                    });
                }
            },
            error: function (xhr, status, error) {
                showErrorWrapper();
            },
            complete: function () {
                $('.demo-api-images-wrapper').removeClass('loading');
                $('.demo-api-images-wrapper').find('.demo-api-images-wrapper-loader').remove();
            }
        });

        var providersBlock = demoApiImagesCreateProvidersBlock();
        var searchBlock = demoApiImagesCreateSearchBlock();

        wrapper.appendChild(grid);

        container.innerHTML = '';
        container.appendChild(providersBlock);
        container.appendChild(searchBlock);
        container.appendChild(wrapper);
        container.appendChild(loadMoreButtonWrapper);
    };

    function demoApiImagesCreateGridItemWithImage(imgData) {
        var gridItem = demoApiImagesCreateGridItem(imgData);
        var gridItemImg = demoApiImagesCreateGridItemImage(imgData);
        gridItem.appendChild(gridItemImg);

        return gridItem;
    }

    function demoApiImagesCreateGridItem(imgData) {
        var gridItem = document.createElement("div");
        gridItem.setAttribute("class", "demo-api-images-media-grid-item");

        gridItem.setAttribute("data-id", imgData.id || '');
        gridItem.setAttribute("data-url", imgData.full_url || '');
        gridItem.setAttribute("data-alt", imgData.alt || '');

        return gridItem;
    };

    function demoApiImagesCreateGridItemImage(imgData) {
        var gridItemImage = document.createElement("img");
        gridItemImage.setAttribute("src", imgData.regular_url);
        gridItemImage.setAttribute("alt", imgData.alt || '');

        return gridItemImage;
    };

    function demoApiImagesCreateProvidersBlock() {

        var providersBlock = document.createElement("div");
        providersBlock.classList.add("demo-api-images-media-toolbar-providers");

        var radioContainer = document.createElement("div");
        radioContainer.classList.add("radio_container");

        if (demo_api_images_set_localize.flaticon_active) {
            var flaticonInput = document.createElement("input");
            flaticonInput.setAttribute("type", "radio");
            flaticonInput.setAttribute("name", "provider-type");
            flaticonInput.setAttribute("id", "flaticon");
            if (selectedProvider == 'flaticon') {
                flaticonInput.setAttribute("checked", "checked");
            }
            var flaticonLabel = document.createElement("label");
            flaticonLabel.setAttribute("for", "flaticon");
            flaticonLabel.innerHTML = "Flaticon";

            radioContainer.appendChild(flaticonInput);
            radioContainer.appendChild(flaticonLabel);
        }

        if (demo_api_images_set_localize.freepik_active) {
            var freepikInput = document.createElement("input");
            freepikInput.setAttribute("type", "radio");
            freepikInput.setAttribute("name", "provider-type");
            freepikInput.setAttribute("id", "freepik");
            if (selectedProvider == 'freepik') {
                freepikInput.setAttribute("checked", "checked");
            }
            var freepikLabel = document.createElement("label");
            freepikLabel.setAttribute("for", "freepik");
            freepikLabel.innerHTML = "Freepik";

            radioContainer.appendChild(freepikInput);
            radioContainer.appendChild(freepikLabel);
        }

        providersBlock.appendChild(radioContainer);


        return providersBlock;
    };

    function demoApiImagesCreateSearchBlock() {
        var searchBlock = document.createElement("div");
        searchBlock.classList.add("demo-api-images-media-toolbar-search");
        searchBlock.classList.add("media-toolbar-primary");
        searchBlock.classList.add("search-form");
        searchBlock.innerHTML = `
                    <label for="media-search-input" class="demo-api-images-search-input-label">Search</label>
                    <span class="spinner"></span>
                    <input type="search" value="${searchSlug || ''}" placeholder="Search free high-resolution photos" autofocus="autofocus" id="demo-api-images-search-input" class="demo-api-images-search">
                        `;

        return searchBlock;
    };

    function demoApiImagesCreateLoadMoreButton() {
        var loadMoreButtonWrapper = document.createElement("div");
        loadMoreButtonWrapper.setAttribute("class", "demo-api-images-loadmore-wrapper");
        var loadMoreButton = document.createElement("button");
        loadMoreButton.innerHTML = "Load more";
        loadMoreButton.setAttribute("class", "button button-hero demo-api-images-loadmore-btn");
        loadMoreButton.style.display = "none";
        loadMoreButton.dataset.paged = 1;
        loadMoreButtonWrapper.appendChild(loadMoreButton);

        return loadMoreButtonWrapper;
    }

    function demoApiImagesBlockLoadButton($element) {
        $element.addClass('demo-api-images-loading');
        $element.prop('disabled', true);
        $element.text(demo_api_images_set_localize.loading);
    }

    function demoApiImagesUnblockLoadButton($element) {
        $element.removeClass('demo-api-images-loading');
        $element.prop('disabled', false);
        $element.text(demo_api_images_set_localize.load_button_text);
    }



    function demoApiImagesBuildSearchParams(paged) {
        var apiUrlSearchParams = new URLSearchParams('');

        switch (selectedProvider) {
            case 'flaticon':
                apiUrlSearchParams.set('limit', 20);
                apiUrlSearchParams.set('page', paged);
                break;
            case 'freepik':
                apiUrlSearchParams.set('locale', 'en');
                apiUrlSearchParams.set('page', paged);
                apiUrlSearchParams.set('limit', 20);
                apiUrlSearchParams.set('order', 'latest');
                break;
        }

        return apiUrlSearchParams;
    }


    $(document.body).on('click', '.demo-api-images-loadmore-btn', function (event) {
        let $btn = $(this);
        demoApiImagesBlockLoadButton($btn);

        let paged = $(this).data('paged');

        console.log(selectedProvider);

        let apiUrlSearchParams = demoApiImagesBuildSearchParams((paged + 1));

        if ($('.demo-api-images-search').val() !== '' && $('.demo-api-images-search').val() !== undefined) {

            switch (selectedProvider) {
                case 'flaticon':
                    apiUrlSearchParams.set('q', $('.demo-api-images-search').val());
                    break;
                case 'freepik':
                    apiUrlSearchParams.set('term', $('.demo-api-images-search').val());
                    break;
            }

            searchSlug = $('.demo-api-images-search').val();

        } else {
            searchSlug = '';
        }

        let grid = document.getElementsByClassName('demo-api-images-media-grid')[0];

        $.ajax({
            url: demo_api_images_set_localize.api_service_url,
            method: "POST",
            data: {
                provider_id: selectedProvider,
                searchQuery: apiUrlSearchParams.toString(),
                searchSlug: searchSlug,
                action: 'get',
                plugin_id: demo_api_images_set_localize.fs_plugin_id,
                wp_site_url: demo_api_images_set_localize.wp_site_url,
                license_id: demo_api_images_set_localize.fs_license_id,
                is_registered: demo_api_images_set_localize.fs_is_registered,
                is_tracking_allowed: demo_api_images_set_localize.fs_is_tracking_allowed
            },
            success: function (data) {
                if(data.success == false) {
                    showErrorWrapper(data.msg);
                } else {
                    data.images.forEach(function (imgData) {
                        var gridItem = demoApiImagesCreateGridItemWithImage(imgData);
                        grid.appendChild(gridItem);

                        demoApiImagesUnblockLoadButton($btn);
                        $btn.data('paged', (paged + 1));
                    });
                }
            },
            error: function (xhr, status, error) {
                showErrorWrapper();
            }
        });

    });

    // Search Handler
    $(document.body).on('keyup change', '.demo-api-images-search', function (event) {
        clearTimeout(timer);
        timer = setTimeout(demoApiImagesSearch, 1500)
    });

    var demoApiImagesSearch = function demoApiImagesSearch() {
        var container = $(".demo-api-images-container")[0];

        var wrapper = document.createElement("div");
        wrapper.classList.add("demo-api-images-wrapper", "loading");

        var wrapperLoader = document.createElement("div");
        wrapperLoader.classList.add("demo-api-images-wrapper-loader");
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));
        wrapperLoader.appendChild(document.createElement("div"));

        wrapper.appendChild(wrapperLoader);

        var grid = document.createElement("div");
        grid.setAttribute("class", "demo-api-images-media-grid");

        var loadMoreButtonWrapper = demoApiImagesCreateLoadMoreButton();

        let paged = 1;

        let apiUrlSearchParams = demoApiImagesBuildSearchParams(paged);

        if ($('.demo-api-images-search').val() !== '' && $('.demo-api-images-search').val() !== undefined) {

            switch (selectedProvider) {
                case 'flaticon':
                    apiUrlSearchParams.set('q', $('.demo-api-images-search').val());
                    break;
                case 'freepik':
                    apiUrlSearchParams.set('term', $('.demo-api-images-search').val());
                    break;
            }

            searchSlug = $('.demo-api-images-search').val();

        } else {
            searchSlug = '';
        }

        $.ajax({
            url: demo_api_images_set_localize.api_service_url,
            method: "POST",
            data: {
                provider_id: selectedProvider,
                searchQuery: apiUrlSearchParams.toString(),
                searchSlug: searchSlug,
                action: 'get',
                plugin_id: demo_api_images_set_localize.fs_plugin_id,
                wp_site_url: demo_api_images_set_localize.wp_site_url,
                license_id: demo_api_images_set_localize.fs_license_id,
                is_registered: demo_api_images_set_localize.fs_is_registered,
                is_tracking_allowed: demo_api_images_set_localize.fs_is_tracking_allowed
            },
            success: function (data) {
                if(data.success == false) {
                    showErrorWrapper(data.msg);
                } else {
                    data.images.forEach(function (imgData) {
                        var gridItem = demoApiImagesCreateGridItemWithImage(imgData);
                        grid.appendChild(gridItem);

                        $('.demo-api-images-loadmore-btn').show();
                    });
                }
            },
            error: function (xhr, status, error) {
                showErrorWrapper();
            },
            complete: function () {
                $('.demo-api-images-wrapper').removeClass('loading');
                $('.demo-api-images-wrapper').find('.demo-api-images-wrapper-loader').remove();
            }
        });

        var providersBlock = demoApiImagesCreateProvidersBlock();
        var searchBlock = demoApiImagesCreateSearchBlock();

        wrapper.appendChild(grid);

        container.innerHTML = "";
        container.appendChild(providersBlock);
        container.appendChild(searchBlock);
        container.appendChild(wrapper);
        container.appendChild(loadMoreButtonWrapper);
    }


    // Change Provider Radion Button
    $(document.body).on('change', '.radio_container input[name="provider-type"]', function (event) {

        selectedProvider = $('.demo-api-images-media-toolbar-providers input[name="provider-type"]:checked').prop('id');
        searchSlug = '';

        init();

    });


    // Download Handler
    $(document.body).on('click', '.demo-api-images-media-grid-item', function (e) {

        e.preventDefault();
        var self = this;

        $(self).append('<div class="demo-api-images-loader"><div></div><div></div></div>');
        $(self).addClass('demo-api-images-loading');

        var target = e.currentTarget;

        // API URL
        var api = demo_api_images_set_localize.root + "demo-api-images/download/";

        // Data Params
        var data = {
            id: target.getAttribute("data-id"),
            image_url: target.getAttribute("data-url"),
            alt: target.getAttribute("data-alt"),
            provider_id: selectedProvider
        };

        $.ajax({
            url: api,
            method: 'POST',
            data: data,
            dataType: 'JSON',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', demo_api_images_set_localize.nonce);
            },
            success: function (response) {

                if (response) {
                    // Successful response from server
                    var success = response.success;
                    var id = response.id;
                    var msg = response.msg;

                    if (success) {
                        apiImagesSetSuccess(self);
                    } else {
                        // Error
                        console.error(msg);
                        apiImagesSetError(self, msg);
                    }
                } else {
                    // Error
                    console.error(demo_api_images_set_localize.error_upload);
                    apiImagesSetError(self);
                }
            },
            error: function (error) {
                console.log(error);
                apiImagesSetError(self);
            },
            complete: function () {
                $(self).find('.demo-api-images-loader').remove();
                $(self).removeClass('demo-api-images-loading');
            }
        });

    });

    function apiImagesSetSuccess(element) {
        $(element).append(`<div class="demo-notify-success">${demo_api_images_set_localize.downloaded}</div>`);
        apiImagesClearNotices();
    }

    function apiImagesSetError(element, msg = '') {
        $(element).append(`<div class="demo-notify-error">${msg || demo_api_images_set_localize.error_message}</div>`);
        apiImagesClearNotices();
    }

    function apiImagesClearNotices() {
        setTimeout(function () {
            $('.demo-notify-success').remove();
            $('.demo-notify-error').remove();
        }, 3000);
    }

    function showErrorWrapper(error = 'Service is temporary unavailable', status = 404) {
        $('.demo-api-images-container').empty();
        $('.demo-api-images-container').append(`<div class="demo-api-images-error-wrapper">${error}</div>`);
    }

    init();

});