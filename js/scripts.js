$(document).ready(function () {

    // Auto-focus on search field when dropdown opens
    $(document).on('select2:open', () => {
        setTimeout(() => {
            document.querySelector('.select2-container--open .select2-search__field')?.focus();
        }, 0);
    });
    $(".pageSlider").owlCarousel({
      items: 1,
      loop:true,
      dots: true,
      smartSpeed: 800, // Duration of the transition
      easing: "easeInOutQuad", // Custom easing function for jQuery Easing
      animateOut: "fadeOut", // Fade out animation
      animateIn: "fadeIn",
      autoplay: true,
      autoplayTimeout: 3000,
      autoplayHoverPause: true,
    });
  // ! Trending Tours slider
  if ($(".js-trendingTours").length) {
    $(".js-trendingTours").owlCarousel({
      items: 4,
      nav: true,
      dots: false,
      margin: 24,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 2,
        },
        960: {
          items: 3,
        },
        1024: {
          items: 4,
        },
      },
    });
  }

  // ! Activities slider
  if ($(".js-activities").length) {
    $(".js-activities").owlCarousel({
      items: 4,
      nav: true,
      dots: false,
      margin: 24,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 2,
        },
        960: {
          items: 3,
        },
        1024: {
          items: 4,
        },
      },
    });
  }
// ! Trending groupTours slider
  if ($(".js-groupTours").length) {
    $(".js-groupTours").owlCarousel({
      items: 2,
      nav: true,
      dots: false,
      margin: 24,
      autoplay: true,
      autoplayTimeout: 5000,
      autoplayHoverPause: true,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 2,
        },
        960: {
          items: 1,
        },
        1024: {
          items: 1,
        },
      },
    });
  }
  // ! Trending Tours slider
  if ($(".js-singleItem").length) {
    $(".js-singleItem").owlCarousel({
      items: 1,
      nav: true,
      dots: false,
      margin: 24,
      smartSpeed: 800, // Duration of the transition
      easing: "easeInOutQuad", // Custom easing function for jQuery Easing
      animateOut: "fadeOut", // Fade out animation
      animateIn: "fadeIn",
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
    });
  }

  // ! Testimonial slider
  if ($(".js-testimonials").length) {
    $(".js-testimonials").owlCarousel({
      items: 2,
      nav: true,
      dots: false,
      margin: 42,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 2,
        },
      },
    });
  }

  // ! Gallery slider
  if ($(".js-gallerySlider").length) {
    $(".js-gallerySlider").owlCarousel({
      items: 4,
      nav: true,
      dots: false,
      margin: 24,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 2,
        },
        768: {
          items: 3,
        },
        960: {
          items: 4,
        },
      },
    });
  }

  // ! Partner Slider
  if ($(".js-partnerCardSlider").length) {
    $(".js-partnerCardSlider").owlCarousel({
      items: 5,
      dots: false,
      autoplay: true,
      margin: 24,
      nav: true,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        420: {
          items: 2,
        },
        560: {
          items: 3,
        },
        768: {
          items: 4,
        },
        960: {
          items: 5,
        },
      },
    });
  }

  // ! Blog slider
  if ($(".js-blogSlider").length) {
    $(".js-blogSlider").owlCarousel({
      items: 3,
      nav: true,
      dots: false,
      margin: 48,
      navText: [
        '<i class="fa-solid fa-arrow-left-long"></i>',
        '<i class="fa-solid fa-arrow-right-long"></i>',
      ],
      responsive: {
        0: {
          items: 1,
        },
        480: {
          items: 1,
        },
        768: {
          items: 2,
        },
        960: {
          items: 3,
        },
      },
    });
  }

  if ($(".subMenus").length) {
    $(".subMenus").hover(function () {
      $(".dropdown-toggle", this).trigger("click");
    });
  }

  // ! Swipe card gallery
  if ($(".js-swiperGallery").length) {
    const swiper = new Swiper(".js-swiperGallery", {
      effect: "cards",
      grabCursor: true,
      navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
      },
    });
  }

  // Select 2 - Initialize only desktop dropdowns initially
  $(".js-advanceSelect").not('#lang-select2, #currency-mobile').select2();
  
  // Fix Select2 in mobile offcanvas menu
  // Don't initialize mobile dropdowns until offcanvas is shown
  var mobileSelectsInitialized = false;
  
  $('#mobileSidebar').on('shown.bs.offcanvas', function () {
    // Destroy any existing instances first
    $('#mobileSidebar .js-advanceSelect').each(function() {
      if ($(this).data('select2')) {
        $(this).select2('destroy');
      }
    });
    
    // Initialize mobile language dropdown
    if ($('#lang-select2').length) {
      $('#lang-select2').select2({
        dropdownParent: $('#mobileSidebar'),
        minimumResultsForSearch: 0,
        width: '100%'
      });
    }
    
    // Initialize mobile currency dropdown
    if ($('#currency-mobile').length) {
      $('#currency-mobile').select2({
        dropdownParent: $('#mobileSidebar'),
        minimumResultsForSearch: 0,
        width: '100%'
      });
    }
    
    // Ensure search input is focusable and clickable
    $('#mobileSidebar .js-advanceSelect').on('select2:open', function(e) {
      var $select = $(this);
      
      // Use multiple timeouts to ensure DOM is ready
      setTimeout(function() {
        var $dropdown = $('#mobileSidebar .select2-dropdown');
        if (!$dropdown.length) {
          $dropdown = $('.select2-dropdown').last();
        }
        
        var $searchField = $dropdown.find('.select2-search__field');
        
        if ($searchField.length && $searchField[0]) {
          var searchInput = $searchField[0];
          
          // Remove any readonly attribute
          searchInput.removeAttribute('readonly');
          searchInput.readOnly = false;
          searchInput.disabled = false;
          
          // Remove any pointer-events or z-index issues
          $searchField.css({
            'pointer-events': 'auto',
            'z-index': '9999',
            'position': 'relative',
            'touch-action': 'manipulation',
            '-webkit-user-select': 'text',
            '-moz-user-select': 'text',
            'user-select': 'text',
            'opacity': '1',
            'visibility': 'visible'
          });
          
          // Make it focusable
          $searchField.attr({
            'tabindex': '0',
            'readonly': false,
            'disabled': false
          });
          
          // Remove any event listeners that might block
          $searchField.off('touchstart touchmove touchend');
          
          // Add click handler to ensure it works
          $searchField.on('click touchstart', function(e) {
            e.stopPropagation();
            $(this).focus();
          });
          
          // Force focus after a short delay
          setTimeout(function() {
            try {
              searchInput.focus();
              // Also trigger a click to ensure mobile browsers recognize it
              var clickEvent = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
              });
              searchInput.dispatchEvent(clickEvent);
            } catch(err) {
              console.log('Focus error:', err);
            }
          }, 100);
          
          // Additional focus attempt
          setTimeout(function() {
            if (document.activeElement !== searchInput) {
              searchInput.focus();
            }
          }, 300);
        }
      }, 200);
    });
    
    mobileSelectsInitialized = true;
  });
  
  // Clean up when offcanvas is hidden
  $('#mobileSidebar').on('hidden.bs.offcanvas', function () {
    // Don't destroy, just mark as not initialized so it reinitializes next time
    mobileSelectsInitialized = false;
  });

  jQuery(".js-calendar-date")
      .datetimepicker({
        format: "m/d/Y",
        timepicker:false,
        minDate: new Date() // Disable past dates
      });
  
      jQuery(".js-calendar-dateTime")
      .datetimepicker({
        format: "m/d/Y H:i",
        minDate: new Date() // Disable past dates
      });

  if ($(".js-number-counter").length) {
    // Function to animate the counter
    const animateCounter = (element, targetValue, duration) => {
      const startValue = parseInt(element.textContent, 10) || 0;
      const increment = (targetValue - startValue) / (duration / 16); // ~60fps
      let currentValue = startValue;

      const updateCounter = () => {
        currentValue += increment;
        if (
          (increment > 0 && currentValue >= targetValue) ||
          (increment < 0 && currentValue <= targetValue)
        ) {
          element.textContent = Math.round(targetValue);
        } else {
          element.textContent = Math.round(currentValue);
          requestAnimationFrame(updateCounter);
        }
      };

      updateCounter();
    };

    // Function to check if element is in the viewport
    const isElementInViewport = (el) => {
      const rect = el.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <=
          (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <=
          (window.innerWidth || document.documentElement.clientWidth)
      );
    };

    // Trigger animation when scrolling
    const debounce = (func, delay) => {
      let timeout;
      return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
      };
    };
    document.addEventListener(
      "scroll",
      debounce(() => {
        const counters = document.querySelectorAll(".js-number-counter");
        counters.forEach((counter) => {
          if (
            isElementInViewport(counter) &&
            !counter.classList.contains("animated")
          ) {
            const targetValue = parseInt(
              counter.getAttribute("data-target"),
              10
            );
            animateCounter(counter, targetValue, 2000); // Animate over 2 seconds
            counter.classList.add("animated"); // Mark as animated
          }
        });
      }, 100)
    );
  }
});
function multicityrenderInputs(multicityIndex) {
    // Select 2
    $(".js-advanceSelect").select2();
  
    // Calendar
    if(multicityIndex>0)
    {
        var lastmulticityIndex=0;
        var departureDate = jQuery('input[name="multicity['+lastmulticityIndex+'][departureDate]"]').val();

        $(".js-calendar-date-"+multicityIndex)
          .datetimepicker({
            format: "m/d/Y",
            timepicker:false,
            minDate: departureDate // Disable past dates
          });
    }
    else
    {
        $(".js-calendar-date-"+multicityIndex)
          .datetimepicker({
            format: "m/d/Y",
            timepicker:false,
            minDate: new Date() // Disable past dates
          });
    }
  }

  function get_act_listing_page(activity_id){
  
    var base_url = $('#crm_base_url').val();
    var b2c_base_url = $('#base_url').val();
    var activity_array = [];
  
    var today = new Date();
    today.setDate(today.getDate());
    var day = today.getDate();
    var month = today.getMonth() + 1;
    var year = today.getFullYear();
    if (day < 10) {
        day = '0' + day;
    }
    if (month < 10) {
        month = '0' + month;
    }
    var today_date = month + "/" + day + "/" + year;
    activity_array.push({
        'activity_city_id': '',
        'activities_id': activity_id,
        'checkDate': today_date,
        'adult': parseInt(1),
        'child': parseInt(0),
        'infant': parseInt(0)
    });
    $.post(base_url + 'controller/b2b_excursion/b2b/search_session_save.php', { activity_array: activity_array }, function(data) {
        window.location.href = b2c_base_url + 'view/activities/activities-listing.php';
    });
  }
function get_tours_data(dest_id, type) {



  var base_url = $('#crm_base_url').val();

  var b2c_base_url = $('#base_url').val();

  var currency = $('#currency').val();

  var tours_array = [];

  if (type == '1' || type == '2') {



      if (type == '1') {



          var tomorrow = new Date();

          tomorrow.setDate(tomorrow.getDate() + 10);

          var day = tomorrow.getDate();

          var month = tomorrow.getMonth() + 1

          var year = tomorrow.getFullYear();

          if (day < 10) {

              day = '0' + day;

          }

          if (month < 10) {

              month = '0' + month;

          }

          var date = month + "/" + day + "/" + year;



          tours_array.push({

              'dest_id': dest_id,

              'tour_id': '',

              'tour_date': date,

              'adult': parseInt(1),

              'child_wobed': parseInt(0),

              'child_wibed': parseInt(0),

              'extra_bed': parseInt(0),

              'infant': parseInt(0)

          });

      } else if (type == '2') {



          tours_array.push({

              'dest_id': dest_id,

              'tour_id': '',

              'tour_group_id': '',

              'adult': parseInt(1),

              'child_wobed': parseInt(0),

              'child_wibed': parseInt(0),

              'extra_bed': parseInt(0),

              'infant': parseInt(0)

          });

      }

      $.post(base_url + 'controller/custom_packages/search_session_save.php', { tours_array: tours_array, currency: currency }, function(data) {

          if (type == '1') {

              window.location.href = b2c_base_url + 'view/tours/tours-listing.php';

          } else if (type == '2') {

              window.location.href = b2c_base_url + 'view/group_tours/tours-listing.php';

          }

      });

  } else if (type == '3') {



      var hotel_array = [];



      var today = new Date();

      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;



      var tomm = new Date();

      tomm.setDate(tomm.getDate() + 1);

      var day = tomm.getDate();

      var month = tomm.getMonth() + 1

      var year = tomm.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var tomm_date = month + "/" + day + "/" + year;
      var final_arr = [];
      final_arr.push({
          rooms : {
              room     : parseInt(1),
              adults   : parseInt(2),
              child    : parseInt(0),
              childAge : []
          }
      });



      hotel_array.push({

          'city_id': '',

          'hotel_id': '',

          'check_indate': today_date,

          'check_outdate': tomm_date,

          'star_category_arr': [],

          'final_arr': JSON.stringify(final_arr)

      });

      $.post(base_url + 'controller/hotel/b2c_search_session_save.php', { hotel_array: hotel_array }, function(data) {

          window.location.href = b2c_base_url + 'view/hotel/hotel-listing.php';

      });

  } else if (type == '4') {

      var activity_array = [];

      var today = new Date();
      today.setDate(today.getDate());
      var day = today.getDate();
      var month = today.getMonth() + 1;
      var year = today.getFullYear();
      if (day < 10) {
          day = '0' + day;
      }
      if (month < 10) {
          month = '0' + month;
      }
      var today_date = month + "/" + day + "/" + year;
      activity_array.push({
          'activity_city_id': '',
          'activities_id': '',
          'checkDate': today_date,
          'adult': parseInt(1),
          'child': parseInt(0),
          'infant': parseInt(0)
      });
      $.post(base_url + 'controller/b2b_excursion/b2b/search_session_save.php', { activity_array: activity_array }, function(data) {
          window.location.href = b2c_base_url + 'view/activities/activities-listing.php';
      });
  }
  else if (type == '5') {

      var today = new Date();
      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;

      var pick_drop_array = [];
      pick_drop_array.push({

          'trip_type': 'oneway',

          'pickup_type': '',

          'pickup_from': '',

          'drop_type': '',

          'drop_to': '',

          'pickup_date': today_date,

          'return_date': '',

          'passengers': '1'

      })

      $.post(base_url + 'controller/b2b_transfer/b2b/search_session_save.php', { pick_drop_array: pick_drop_array }, function(data) {

          window.location.href = b2c_base_url + 'view/transfer/transfer-listing.php';

      });

  } else if (type == '6') {

      var visa_array = [];

      visa_array.push({

          'country_id': ''

      });

      $.post(base_url + 'controller/visa_master/search_session_save.php', { visa_array: visa_array }, function(data) {

          window.location.href = b2c_base_url + 'view/visa/visa-listing.php';

      });

  } else if (type == '7') {

      var today = new Date();

      today.setDate(today.getDate());

      var day = today.getDate();

      var month = today.getMonth() + 1;

      var year = today.getFullYear();

      if (day < 10) {

          day = '0' + day;

      }

      if (month < 10) {

          month = '0' + month;

      }

      var today_date = month + "/" + day + "/" + year;

      var ferry_array = [];

      ferry_array.push({

          'from_city': '',

          'to_city': '',

          'travel_date': today_date,

          'adult': parseInt(1),

          'children': parseInt(0),

          'infant': parseInt(0)

      })

      $.post(base_url + 'controller/ferry/search_session_save.php', { ferry_array: ferry_array }, function(data) {

          window.location.href = b2c_base_url + 'view/ferry/ferry-listing.php';

      });

  }

}

function get_selected_currency() {
    var base_url = $("#base_url").val();
  
    var currency_id = $("#currency").val();
    
    //Set selected currency in php session also
  
    $.post(
      base_url + "view/set_currency_session.php",
      { currency_id: currency_id },
      function (data) {}
    );
  
    if (typeof Storage !== "undefined") {
      if (localStorage) {
        localStorage.setItem("global_currency", currency_id);
      } else {
        window.sessionStorage.setItem("global_currency", currency_id);
      }
    }
  
    // Call respective currency converter according active page url
  
    var current_page_url = document.URL;
  
    var tours_pageurl = base_url + "view/tours/tours-listing.php";
  
    if (
      current_page_url.split(base_url + "package_tours").length - 1 == 1 ||
      tours_pageurl == current_page_url
    ) {
      tours_page_currencies(current_page_url);
    }
  
    var tours_pageurl = base_url + "view/group_tours/tours-listing.php";
  
    if (
      current_page_url.split(base_url + "group_tours").length - 1 == 1 ||
      tours_pageurl == current_page_url
    ) {
      group_tours_page_currencies(current_page_url);
    }
  
    location.reload();
  }

  function get_hotel_listing_page(hotel_id){
    
  var base_url = $('#crm_base_url').val();
  var b2c_base_url = $('#base_url').val();
  var hotel_array = [];

  var today = new Date();
  today.setDate(today.getDate());
  var day = today.getDate();
  var month = today.getMonth() + 1;
  var year = today.getFullYear();
  if (day < 10) {
      day = '0' + day;
  }
  if (month < 10) {
      month = '0' + month;
  }
  var today_date = month + "/" + day + "/" + year;

  var tomm = new Date();
  tomm.setDate(tomm.getDate() + 1);
  var day = tomm.getDate();
  var month = tomm.getMonth() + 1
  var year = tomm.getFullYear();
  if (day < 10) {
      day = '0' + day;
  }
  if (month < 10) {
      month = '0' + month;
  }
  var tomm_date = month + "/" + day + "/" + year;

  var final_arr = [];
  final_arr.push({
      rooms : {
          room     : parseInt(1),
          adults   : parseInt(2),
          child    : parseInt(0),
          childAge : []
      }
  });

  hotel_array.push({
      'city_id': '',
      'hotel_id': hotel_id,
      'check_indate': today_date,
      'check_outdate': tomm_date,
      'star_category_arr': [],
      'final_arr': JSON.stringify(final_arr)
  });
  $.post(base_url + 'controller/hotel/b2c_search_session_save.php', { hotel_array: hotel_array }, function(data) {
      window.location.href = b2c_base_url + 'view/hotel/hotel-listing.php';
  });
}

function error_msg_alert(message, base_url = '') {

  if (base_url == '') {

      var base_url1 = $('#base_url').val() + 'Tours_B2B/notification_modal.php';

  } else {

      var base_url1 = base_url + 'notification_modal.php';

  }

  var class_name = 'alert-danger';

  $.post(base_url1, { message: message, class_name: class_name }, function(data) {

      $('#site_alert').html(data);

  });

}



function success_msg_alert(message, base_url = '') {

  if (base_url == '') {

      var base_url1 = $('#base_url').val() + 'Tours_B2B/notification_modal.php';

  } else {

      var base_url1 = base_url + 'notification_modal.php';

  }

  var class_name = 'alert-success';

  $.post(base_url1, { message: message, class_name: class_name }, function(data) {

      $('#site_alert').html(data);

  });

}