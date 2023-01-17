/* import WooCommerceRestApi from  "../../node_modules/@woocommerce/woocommerce-rest-api"

const api = new WooCommerceRestApi({
	url: "http://staging-hoodsly.kinsta.cloud/",
	consumerKey: "ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a",
	consumerSecret: "cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c",
	version: "wc/v3"
});

api.get("products/20782")
	.then((response) => {
		console.log(response.data);
	})
	.catch((error) => {
		console.log(error.response.data);
	}); */

(function ($) {

    /*** Sticky header */
    $(window).scroll(function () {
        if ($("body").scrollTop() > 0 || $("html").scrollTop() > 0) {
            $(".dashboard__header").addClass("sticky-header");
        } else {
            $(".dashboard__header").removeClass("sticky-header");
        }
    });

    $(document).on('click', '.dashboard__header .navbar-toggle', function (e) {
        $(this).toggleClass('in');
        $('.hoodslyhub-user-dashboard').toggleClass('hoodslyhub-navbar-toggle');
    });

    var myElement = document.getElementById('simplebar');
    new SimpleBar(myElement, {
        autoHide: true
    });

    var historySimplebar = document.getElementById('history-simplebar');
    var notification_scroll = document.getElementById('order_notification_list');
    var CliamSimplebar    = document.querySelectorAll('.cliam-simplebar');

    if (historySimplebar) {
        new SimpleBar(historySimplebar, {
            autoHide: true
        });
    }

    if (notification_scroll) {
        new SimpleBar(notification_scroll, {
            autoHide: true
        });
    }
    if (CliamSimplebar) {
        $('.cliam-simplebar').each(function(){
            new SimpleBar($(this)[0], {autoHide: true});
        });
    }

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
        });

        $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
            $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
        });
    });

    /*** Header height = gutter height */
    function setGutterHeight() {
        var header = document.querySelector('.dashboard__header'),
            gutter = document.querySelector('.header-gutter');
        if (gutter) {
            gutter.style.height = header.offsetHeight + 'px';
        }
    }

    window.onload = setGutterHeight;
    window.onresize = setGutterHeight;

    $(document).on('click', '.hoodslyhub-delete-order', function (e) {
        e.preventDefault();
        //console.log($(this))
        let order_id = $(this).data('orderid');
        let nonce = $(this).data('nonce');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve) {
                    $.ajax({
                        type: 'post',
                        url: ajaxRequest.ajaxurl,
                        data: {
                            action: 'hoodslyhub_delete_order',
                            nonce: nonce,
                            id: order_id
                        },
                        success: function (data) {
                            if (data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Your Order has been deleted..',
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong.',
                            })
                        }
                    })
                });
            },
            allowOutsideClick: false
        });
    })

    $(document).on('click', '.hoodslyhub-damage-claim', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        let origin = $(this).data('origin');
        let orderid = $(this).data('orderid');
        let email = $(this).data('email');

        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'hoodslyhub_damage_claim_email_sent',
                nonce: nonce,
                id: post_id,
                orderid: orderid,
                origin: origin,
                email: email,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Sending...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sent!',
                        text: 'Email has been sent successfully.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Emails not sending. Something went wrong.',
                    showConfirmButton: false,
                    timer: 2000
                })
            }

        })
    })
    $('.submit_vent_tracking').on('click', function (e) {
        e.preventDefault();
        var vent_shipping_method = $('.vent_shipping_method').val();
        var vent_tracking_num = $('.vent_tracking_num').val();
        var post_id = $('.post_id').val();
        var vent_email = $('.vent_email').val();
        console.log(vent_email);
        if (vent_tracking_num !== "" && vent_shipping_method !== "Select..") {
            $('.alert-danger').css("display", "none")
            //Ajax Function to send a get request
            $.ajax({
                type: "POST",
                url: hub_obj.ajaxurl,
                data: {
                    action: "add_ventilation_tracking",
                    vent_shipping_method: vent_shipping_method,
                    vent_tracking_num: vent_tracking_num,
                    post_id: post_id,
                    vent_email: vent_email,
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Sending...',
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false,

                    });
                    Swal.showLoading();
                },
                success: function (response) {
                    //if request if made successfully then the response represent the data
                    $("#result").empty().append(response);
                    if (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sent!',
                            text: 'Email has been sent successfully.',
                            showConfirmButton: false,
                            timer: 2000
                        })
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Emails not sending. Something went wrong.',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            });
        } else {
            $('.alert-danger').css("display", "block")
        }
    })

    /**
     * Select Field Custom
     */
    $('.order-select').each(function () {
        var $this = $(this),
            numberOfOptions = $(this).children('option').length;
        var overflow = numberOfOptions > 5 ? 'overflow-y' : '';
        $this.addClass('select-hidden');
        $this.wrap('<div class="select"></div>');
        $this.after('<div class="select-styled"></div>');

        var $styledSelect = $this.next('div.select-styled');
        $styledSelect.text($this.children('option').eq(0).text());

        var $list = $('<ul />', {
            'class': 'select-options'
        }).insertAfter($styledSelect);

        for (var i = 0; i < numberOfOptions; i++) {
            $('<li />', {
                text: $this.children('option').eq(i).text(),
                rel: $this.children('option').eq(i).val()
            }).appendTo($list);
        }

        var $listItems = $list.children('li');

        $styledSelect.click(function (e) {
            e.stopPropagation();
            $('div.select-styled.active').not(this).each(function () {
                $(this).removeClass('active').next('ul.select-options').hide();
            });
            $(this).toggleClass('active').next('ul.select-options').addClass(overflow).toggle();
        });

        $listItems.click(function (e) {
            e.stopPropagation();
            $styledSelect.text($(this).text()).removeClass('active');
            $this.val($(this).attr('rel'));
            $('select option').removeAttr('selected');
            $('select option[value="' + $(this).attr('rel') + '"]').attr('selected', 'selected');
            // Only Woo Orderby
            if ($this.hasClass('orderby')) {
                $(this).closest('form').submit();
            }
            $list.hide();
        });

        $(document).click(function () {
            $styledSelect.removeClass('active');
            $list.hide();
        });
    });

    /**
     * Shop claim Action
     */
    $(document).on('click', '.shop_claim_approved', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce    = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'shop_claim_approved_request',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Approving...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: 'Approved shop claim',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Approving failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Approving failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /**
     * Shop Custom Color match Action - Received
     */
    $(document).on('click', '.wrh_ccm_received', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wrh_ccm_received_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Receiving...',
                    text: 'Receiving Custom Color Match Order.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: 'Received custom color match samples.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Receiving proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Receiving proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /**
     * Shop Custom Color match Action - Send To Be Matched
     */
    $(document).on('click', '.ccm_send_to_be_matched', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wrh_ccm_send_to_be_matched_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Sending...',
                    text: 'Sending Custom Color Match Samples for Matched.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Sent to be matched proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /**
     * Shop Custom Color match Action - Matched
     */
    $(document).on('click', '.ccm_matched', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wrh_ccm_matched_action',
                nonce: nonce,
                post_id: post_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Matching on the process.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Matched!',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Order In Production',
                            showConfirmButton: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            //timer: 3000
                        }).then(() => {
                            location.reload();
                        });
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Matched proses failed. Something went wrong.',
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    location.reload();
                });
            }
        })
    })

    /*
     * Order pending to Productuion
     * */
    $(document).on('click', '.wrh-pending-status-action', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let order_id = $(this).data('orderid');
        let nonce = $(this).data('nonce');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'wrh_order_pending_to_production',
                nonce: nonce,
                post_id: post_id,
                order_id: order_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Order Processing for Production.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Received!',
                        text: data.msg,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Order proses failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Order proses failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /*
     * Hub Custom Color match Action
     * */
    $(document).on('click', '.request_vent', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let nonce = $(this).data('nonce');
        let order_id = $(this).data('orderid');
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'request_ventilation',
                nonce: nonce,
                postid: post_id,
                orderid: order_id,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Requesting...',
                    text: 'Requesting Ventilation.',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Requested!',
                        text: 'Requested ventilations successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Requested proses failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Requested proses failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /*
     * Ventilation order Action WRH - Picked
     */
    $(document).on('click', '.quick-ship-wrh-picked, .quick-ship-wrh-delivered', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let orderid = $(this).data('orderid');
        let nonce = $(this).data('nonce');
        let action_type = $(this).html()
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'quick_ship_order_action_status',
                nonce: nonce,
                post_id: post_id,
                action_type: action_type,
                orderid: orderid,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: action_type.trim(),
                        text: 'Order ' + action_type.trim() + ' successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Picked proses failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    $.ajax({
        type: 'get',
        url: 'https://staging-hoodsly.kinsta.cloud/wp-json/wc/v3/products/20782/variations?consumer_key=ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a&consumer_secret=cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c',
        data: '',
        contentType: "application/json",
        dataType: 'json',
        success: function(result){
            let $new_var_item = [];
            result.forEach(element => {
                
                $new_var_item[element.id] = 
                `<tr style="background-color: #0E9CEE1a;">
                <td data-title="Order Id">
                <form action="" method="post" name="send_variation_stock">
                    <input type="number" name="variation_stock_qty" class="form-control variation_stock_hub" id="" value="${element.stock_quantity}">
                    <input type="hidden" class="variation_id" name="variation_id" id="" value="${element.id}">
                    <input type="submit" class="save_stock_var" name="update_variation_stock" type="button" value="✓" />
                </form>
                </td>
                <td data-title="Items" id="ordered_items">Quick Shipping Options</td>
                <td>${element["attributes"][0]["option"]}</td>
                </tr>`
            });
            $('.hide_after').css("display", "none");
            $('#stock_inventory').append(`${$new_var_item}`)
        }
    })
    $(document).on('click', '.save_stock_var', function (e) {
        e.preventDefault();
        
        let $variation_id = $(this).siblings(".variation_id").val()
        let $stock_quantity = $(this).siblings(".variation_stock_hub").val()
        let $_stock_quantity = JSON.stringify({
            stock_quantity: $stock_quantity
        })
        $.ajax({
            type: 'PUT',
            url: 'https://staging-hoodsly.kinsta.cloud/wp-json/wc/v3/products/20782/variations/'+$variation_id+'?consumer_key=ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a&consumer_secret=cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c',
            data: $_stock_quantity,
            contentType: "application/json",
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    text: 'Variation Stock Updating.....',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function(result){
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Variation Stock Updated Successfully',
                    showConfirmButton: false,
                    timer: 3000
                });
                $(this).siblings(".variation_stock_hub").value = $stock_quantity;
                let $now_date = new Date().toLocaleString();
                if($stock_quantity == 0){
                    $.ajax({
                        type: 'post',
                        url: ajaxRequest.ajaxurl,
                        data: {
                            action: 'poke_wrh_if_zero',
                            variation_id: $variation_id,
                        },
                        success: function(result){
                            $('.order_notification_list .simplebar-content').prepend('<li>Quick Ship Product Variations ('+$variation_id+') stock out <span class="comment_date"><i>'+$now_date+'</li><hr>')
                        }
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Sorry..... Update Process Failed.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    var ppp = 10; // Post per page
    var pageNumber = 1;
    
    
    function load_posts(){
        pageNumber++;
        var str = '&pageNumber=' + pageNumber + '&ppp=' + ppp + '&action=load_more_post_ajax';
        $.ajax({
            type: "POST",
            dataType: "html",
            url: ajaxRequest.ajaxurl,
            data: str,
            beforeSend: function () {
                $('#more_posts').after(`<div class="load_more_notification">
                <div class="loadingio-spinner-spinner-jcq5xlo2ig">
                    <div class="ldio-wdqegidkzlf">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>`)
            },
            success: function(data){
                var $data = $(data);
                console.log($data)
                if($data.length){
                    $(".order_notification_list .simplebar-content").append($data);
                    //$("#more_posts").hide();
                    //$("#more_posts").attr("disabled",false); // Uncomment this if you want to disable the button once all posts are loaded
                    //$("#more_posts").hide(); // This will hide the button once all posts have been loaded
                    $('.load_more_notification').css('display', 'none')

                    //$('#more_posts').fadeToggle();
                    $('.order_notification_list ').animate({
                        scrollTop: $('#more_posts').offset().top + 350
                    }, 1000);

                } else{
                    $("#more_posts").attr("disabled",true);
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                $loader.html(jqXHR + " :: " + textStatus + " :: " + errorThrown);
            }
    
        });
        return false;
    }
    
    $("#more_posts").on("click",function(){ // When btn is pressed.
        $("#more_posts").attr("disabled",true); // Disable the button, temp.
        load_posts();
        $(this).insertAfter('.order_notification_list .simplebar-content'); // Move the 'Load More' button to the end of the the newly added posts.
    });

    $(".order_notification_list").click(function (event) {
		event.stopImmediatePropagation();
	});

	$('.select_status').on('change', function () {
		var orderid_array = [];
		var postid_array = [];
		var origin = [];
        $('.bulk_check:checked').each(function(i){
			orderid_array[i] = $(this).data("orderid");
			postid_array[i] = $(this).data("postid").trim();
            origin[i] = $(this).data("ordersource");
        });
		console.log(origin)
		if(orderid_array == ''){
			return false;
		}
		$.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'update_order_status_bulk',
                orderid_array: orderid_array,
                postid_array: postid_array,
                origin: origin,
                status_label: $(this).val()
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Status',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: data.msg,
                        imageUrl: data.imageurl,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                    }).then(() => {
                        //location.reload();
                        $('#select_status option:first').prop('selected',true)
                    });

                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                }).then(() => {
                    //location.reload();
                    $('#select_status option:first').prop('selected',true)
                });
            }
        })
	});

	$('.bulk_edit').on('click', function () {
		var orderurl_array = [];
        $('.bulk_check:checked').each(function(i){
			orderurl_array[i] = $(this).data("orderurl");
        });
		console.log(orderurl_array)
		if(orderurl_array == ''){
			return false;
		}
		orderurl_array.forEach(function(item) {
			window.open(item);
		});
	})
	$('.bulk_download_bol').on('click', function () {
		/* var orderurl_array = [];
        $('.bulk_check:checked').each(function(i){
			orderurl_array[i] = $(this).data("orderurl");
        });
		console.log(orderurl_array)
		if(orderurl_array == ''){
			return false;
		} */
		window.open('http://localhost/wrh_hub/wp-content/uploads/bol/Bulk-Bol_List.pdf');
	})

    /**
     * Wrh Order Pagination
     */
    $('#wrh-order-list').on('click', '#wrhPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wrhPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "wrh_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wrh-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#wrh-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wrhPaginate').html(data);
            }
        });
    });
    //End WRH Order Pagination

    /**
     * WRH pending Order Pagination
     */
    $('#pending-order-list').on('click', '#pendingPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="half_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#pendingPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pending_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#pending-order-list .has--custom-color tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#pending-order-list .has--custom-color tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#pendingPaginate').html(data);
            }
        });
    });
    //End pending Order Pagination

    /**
     * WRH Completed Order Pagination
     */
    $('#completed-order-list').on('click', '#completedPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#completedPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "completed_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#completed-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#completed-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#completedPaginate').html(data);
            }
        });
    });
    //End Completed Order Pagination

    /**
     * WRH CCM Order Pagination
     */
    $('#ccm-order-list').on('click', '#ccmPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="half_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ccmPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "ccm_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#ccm-order-list .has--custom-color tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#ccm-order-list .has--custom-color tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ccmPaginate').html(data);
            }
        });
    });
    //End CCm Order Pagination

    /**
     * WRH Vent Order Pagination
     */
    $('#vent-order-list').on('click', '#ventPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ventPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "vent_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#vent-order-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#vent-order-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ventPaginate').html(data);
            }
        });
    });
    //End Vent Order Pagination

    /**
     * WRH Vent completed Order Pagination
     */
    $('#vent-completed-list').on('click', '#ventcompletedPaginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="full_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#ventcompletedPaginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "vent_completed_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#vent-completed-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#vent-completed-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#ventcompletedPaginate').html(data);
            }
        });
    });
    //End Vent completed Order Pagination

    /**
     * WRH Quick Order Pagination
     */
    $('#wrh-quick-shipping-list').on('click', '#wrh_quick_shiping_list_paginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wrh_quick_shiping_list_paginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "quick_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wrh-quick-shipping-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#wrh-quick-shipping-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wrh_quick_shiping_list_paginate').html(data);
            }
        });
    });
    //End Vent completed Order Pagination

    /**
     * WRH Quick completed Order Pagination
     */
    $('#wrh-quick-completed-list').on('click', '#wrh_quick_completed_list_paginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wrh_quick_completed_list_paginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "quick_completed_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wrh-quick-completed-list .table-order tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#wrh-quick-completed-list .table-order tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wrh_quick_completed_list_paginate').html(data);
            }
        });
    });
    //End Vent completed Order Pagination

    /**
     * WRH Quick Order with vent Pagination
     */
    $('#wrh-quick-vent-list').on('click', '#wrh_quick_vent_list_paginate a', function (e) {
        e.preventDefault();
        let ajaxDiv = '<div class="side_p_ajax-loader" ><div class="hidden-loader__spin"></div></div>';

        var hub_paged = 1;
        if($(this).hasClass('prev')){
            hub_paged = $(this).siblings('.page-numbers.current').prev().text();
        }else if($(this).hasClass('next')){
            hub_paged = $(this).siblings('.page-numbers.current').next().text();
        }else {
            hub_paged = $(this).text()
        }

        var max_page = $("#wrh_quick_vent_list_paginate").data("max_num_pages");

        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "quick_vent_order_table_pagination",
                hub_paged: hub_paged,
            },
            beforeSend: function () {
                $('#wrh-quick-vent-list .has--custom-color tbody').html(ajaxDiv);
            },
            success: function (data) {
                $('#wrh-quick-vent-list .has--custom-color tbody').html(data);
                $('[data-toggle="tooltip"]').tooltip({
                    html: true,
                });
                $('.table .files[data-toggle="tooltip"]').on('show.bs.tooltip', function () {
                    $($(this).data('bs.tooltip').tip).addClass('tooltip-files');
                });
            }
        });


        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: {
                action: "pagination_ajax",
                hub_paged: hub_paged,
                max_page: max_page,
            },
            success: function (data) {
                $('#wrh_quick_vent_list_paginate').html(data);
            }
        });
    });
    //End WRH Quick Order with vent Pagination

    /*
     * Accessory order mark order completed
     */
    $(document).on('click', '.mark-as-completed', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let orderid = $(this).data('orderid');
        let nonce = $(this).data('nonce');
        let action_type = $(this).html()
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'accessory_order_action',
                nonce: nonce,
                post_id: post_id,
                action_type: action_type,
                orderid: orderid,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: action_type.trim(),
                        text: 'Order ' + action_type.trim() + ' successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Picked process failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })
    $('.print_order').click(function(){
        //var divToPrint = document.getElementById("printTable");
        let post_id = $(this).data('postid');
        let orderid = $(this).data('orderid');
        var popupWin         = window.open("", "_blank", "width=300,height=500");
        //newWin= window.open("");
        popupWin.document.open();
        popupWin.document.write(orderid);
        popupWin.document.close();
   });

    /**
     * Damage image magnific popup
     */
    $('.gallery-popup-item').magnificPopup({
        type: 'image',
        // midClick: true,
        // fixedBgPos: true,
        // removalDelay: 500,
        // fixedContentPos: true,
        // tLoading: 'Loading image #%curr%...',
        // gallery: {
        //     enabled: true,
        //     preload: [0, 1],
        //     navigateByImgClick: true,
        // },
        // image: {
        //     tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
        //     titleSrc: function (item) {
        //         return item.el.find('img').attr('alt');
        //     },
        // },
        // callbacks: {
        //     beforeOpen: function () {
        //         this.st.image.markup = this.st.image.markup.replace(
        //             'mfp-figure',
        //             'mfp-figure mfp-with-anim'
        //         );
        //         this.st.mainClass    =
        //             'mfp-move-from-top vertical-middle mfp-popup-gallery';
        //     },
        //     buildControls: function () {
        //         // re-appends controls inside the main container
        //         this.arrowLeft.appendTo(this.contentContainer);
        //         this.arrowRight.appendTo(this.contentContainer);
        //         this.currTemplate.closeBtn.appendTo(this.contentContainer);
        //     },
        // },
    });

    /*
     * Accessory order mark order completed
     */
    $(document).on('click', '.quick-ship-wrh-ready_pick', function (e) {
        e.preventDefault();
        
        let $variation_id = $(this).data("variation_id")
        let $stock_quantity = $(this).data("req_stock")
        //let $size_attr = $(this).data("size_attr")
        let $size_attr = $(this).siblings(".size_attr").val()
        console.log($variation_id);
        console.log($stock_quantity);
        //console.log($_stock_quantity)
        /* $.ajax({
            type: 'get',
            url: 'https://staging-hoodsly.kinsta.cloud/wp-json/wc/v3/products/20782/variations/'+$variation_id+'?consumer_key=ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a&consumer_secret=cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c',
            data: '',
            contentType: "application/json",
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    text: 'Variation Stock Request Accepting.....',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function(result){
                if(result.stock_quantity){
                    let $_stock_quantity = JSON.stringify({
                        stock_quantity: $stock_quantity + result.stock_quantity
                    })
                    $.ajax({
                        type: 'PUT',
                        url: 'https://staging-hoodsly.kinsta.cloud/wp-json/wc/v3/products/20782/variations/'+$variation_id+'?consumer_key=ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a&consumer_secret=cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c',
                        data: $_stock_quantity,
                        contentType: "application/json",
                        dataType: 'json'
                    })
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Variation Stock Request Accepted',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Sorry..... Update Process Failed.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        }) */
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'send_to_local_del',
                variation_id: $variation_id,
                stock_quantity: $stock_quantity,
                size_attr: $size_attr,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    text: 'Sending to local delivery page.....',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                });
                Swal.showLoading();
            },
            success: function(result){
                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: 'Sent to Local Delivery Page In HoodslyHub',
                    showConfirmButton: false,
                    timer: 3000
                })
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Sorry..... Update Process Failed.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /*
     * Deny edit order
     */
    $(document).on('click', '.deny_new_changes', function (e) {
        e.preventDefault();
        let post_id = $(this).data('postid');
        let orderid = $(this).data('orderid');
        let btn_txt = $(this).html();
        console.log(btn_txt);
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'order_new_changes_approve_deny',
                post_id: post_id,
                orderid: orderid,
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: btn_txt.trim(),
                        text:  btn_txt.trim() + ' successfull.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Picked process failed. Something went wrong.',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })
    
    /**
     * 
     */
    $(document).on('click', '.bulk_move_order', function(e){
        //e.stopPropagation()
        var orderid_array;
        var postid_array;
        var status;
        var date;
        
        if ($(this).is(":checked"))
        {
            orderid_array = $(this).data("orderid");
            postid_array = $(this).data("postid");
            status = $(this).data("status");
            date = $(this).data("date");
            // it is checked
            var _html = `<tr class="">
                            <td data-orderid="${orderid_array}" data-postid="${postid_array}">${orderid_array}</td>
                            <td>${status}</td>
                            <td>${date}</td>
                        </tr>`;
            $(".order_items").append(_html);
        }
       
    })
    
     /*
    Hub Search
    */
    $('[name=hub-search]').on('keyup', function () {
        var searchString = $(this).val();
        var ajaxDiv      = $('#hidden-loader');
        var resultDiv    = $('#hub_searchresult');
        //$(document).off('click');
        
        if (searchString) {
            resultDiv.html('');
            ajaxDiv.css('display', 'block');
            var data = {
                query: searchString,
                action: 'hub_search_handler'
            };
            $.ajax({
                url: ajaxDiv.data('ajaxurl'),
                data: data,
                type: 'POST',
                dataType: "html",
                success: function (data) {
                    if (data) {
                        resultDiv.html(data);
                        ajaxDiv.css('display', 'none');
                    } else {
                        resultDiv.html('<p class="hub-noresult">There is no order ID to your query.</p>')
                        ajaxDiv.css('display', 'none');
                    }
                }
            });
        } else {
            resultDiv.html('');
            ajaxDiv.css('display', 'none');
        }
    });
    $('.bulk_move_request').on('change', function(){
        var orderid_array = [];
        var postid_array = [];
        $('.order_items tr').each(function(i, element){
			orderid_array[i] = $(this).children('td').data("orderid");
			postid_array[i] = $(this).children('td').data("postid");
        });
        if(orderid_array == ''){
			return false;
		}
        $.ajax({
            type: 'post',
            url: ajaxRequest.ajaxurl,
            data: {
                action: 'update_order_status_bulk',
                orderid_array: orderid_array,
                postid_array: postid_array,
                origin:origin,
                status_label: $(this).val()
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated Status',
                        text: 'Order Updated Status Successfully.',
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...Failed',
                        text: 'Bulk Updating Status Failed...Please try again',
                        showConfirmButton: false,
                        timer: 3000
                    })
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'failed. Something went wrong.',
                    showConfirmButton: false,
                    timer: 3000
                })
            }
        })
    })

    /*
   *  Incoming Order Proof of drop off image upload action
   */
    $('.wrh_packaged_upload_proof_action').on('submit', function (e) {
        e.preventDefault();
        let orderid   = $(this).data('orderid');
        let formData  = new FormData();
        let form_data = $(this).serializeArray();
        $.each(form_data, function (key, input) {
            formData.append(input.name, input.value);
        });
        formData.append("file", $('#drop_off_image_upload-' + orderid)[0].files[0]);
        $.ajax({
            type: "POST",
            url: ajaxRequest.ajaxurl,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                Swal.fire({
                    title: 'Please Wait...',
                    showConfirmButton: false,
                    allowEscapeKey: false,
                    allowOutsideClick: false,

                });
                Swal.showLoading();
            },
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified!',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid',
                        text: data.msg,
                        showConfirmButton: true,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        //timer: 3000
                    }).then(() => {
                        document.getElementById("wrh_packaged_upload_proof_action").reset();
                    });
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...Failed',
                    text: 'Proses failed. Something went wrong.',
                    showConfirmButton: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    //timer: 3000
                }).then(() => {
                    document.getElementById("wrh_packaged_upload_proof_action").reset();
                });
            }
        })
    })
    /**
     * Incoming Order Proof of drop off image upload Modal input field reset if its closed
     */
    $('.wrh_packaged_upload_proof_wrapper').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })


    
}(jQuery));