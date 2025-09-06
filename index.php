<?php
include "config.php";
global $app_contact_no;
$b = 'base6' . '4_decode';
$service = $_GET['service'];
$_SESSION['page_type'] = 'index';
include 'layouts/header.php'; //Include header
?>

<!-- *** Banner slider *** -->
<section class="c-bannerAndFilter with-slider">
  <div class="c-banner type-01">

    <!-- YouTube Video Background -->
    <div class="video-wrapper position-absolute top-0 start-0 w-100 h-100">
      <iframe class="yt-video"
        src="https://www.youtube.com/embed/Wcd6r97fOgo?autoplay=1&mute=1&controls=0&showinfo=0&loop=1&playlist=Wcd6r97fOgo&rel=0&modestbranding=1"
        frameborder="0"
        allow="autoplay; fullscreen"
        allowfullscreen
        title="Holiday Tour Video"></iframe>
    </div>
  </div>
  <!-- *** Slider End *** -->
  </div>
</section>
<!-- *** Banner slider End *** -->
<!-- ***** Filter Section ***** -->
<section class="c-filter">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <div class="filterWrapper">
          <!-- ***** Filter Tabs ***** -->
          <div class="c-filterTabs">
            <ul class="nav nav-tabs parentNav" id="myTab" role="tablist">

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active filterButton fs-7"
                  id="holiday-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#holiday-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="holiday-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-umbrella-beach me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Holiday</span>
                </button>
              </li>

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="groupTour-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#groupTour-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="groupTour-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-users me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Group Tour</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="hotel-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#hotel-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="hotel-tab-pane"
                  aria-selected="true">
                  <i class="fa-solid fa-hotel me-2 fs-8"></i>
                  <span class="fw-medium">Hotel</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="flight-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#flight-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="flight-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-plane-departure me-2 fs-8"></i>
                  <span class="fw-medium">Flight</span>
                </button>
              </li>

              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="activity-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#activity-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="activity-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-sailboat me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Activity</span>
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link filterButton fs-7"
                  id="transfer-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#transfer-tab-pane"
                  type="button"
                  role="tab"
                  aria-controls="transfer-tab-pane"
                  aria-selected="false">
                  <i class="fa-solid fa-car me-2 me-2 fs-8"></i>
                  <span class="fw-medium">Transfer</span>
                </button>
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <!-- ***** Flight ***** -->
              <div
                class="tab-pane fade"
                id="flight-tab-pane"
                role="tabpanel"
                aria-labelledby="flight-tab"
                tabindex="0">
                <?php include 'view/flight/flight-search.php'; ?>
              </div>
              <!-- ***** Flight End ***** -->

              <!-- ***** Hotel ***** -->
              <div
                class="tab-pane fade"
                id="hotel-tab-pane"
                role="tabpanel"
                aria-labelledby="hotel-tab"
                tabindex="0">
                <?php include 'view/hotel/hotel-search.php'; ?>
              </div>
              <!-- ***** Hotel End ***** -->

              <!-- ***** Group Tour ***** -->
              <div
                class="tab-pane fade"
                id="groupTour-tab-pane"
                role="tabpanel"
                aria-labelledby="groupTour-tab"
                tabindex="0">
                <?php include 'view/group_tours/tours-search.php'; ?>
              </div>
              <!-- ***** Group Tour End ***** -->

              <!-- ***** Holiday Tour ***** -->
              <div
                class="tab-pane fade show active"
                id="holiday-tab-pane"
                role="tabpanel"
                aria-labelledby="holiday-tab"
                tabindex="0">
                <?php include 'view/tours/tours-search.php'; ?>
              </div>
              <!-- ***** Holiday Tour ***** -->

              <!-- ***** Activity Tour ***** -->
              <div
                class="tab-pane fade"
                id="activity-tab-pane"
                role="tabpanel"
                aria-labelledby="activity-tab"
                tabindex="0">
                <?php include 'view/activities/activities-search.php'; ?>
              </div>
              <!-- ***** Activity Tour End ***** -->

              <!-- ***** Transfer Tour ***** -->
              <div
                class="tab-pane fade"
                id="transfer-tab-pane"
                role="tabpanel"
                aria-labelledby="transfer-tab"
                tabindex="0">
                <?php include 'view/transfer/transfer-search.php'; ?>
              </div>
              <!-- ***** Activity Tour End ***** -->
            </div>
          </div>
          <!-- ***** Filter Tabs End ***** -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- *** Banner slider End *** -->
<?php
$popularPackages = $themeData->getPopularPackages();
if ($popularPackages && count($popularPackages) > 0) {
?>
  <!-- ***** Best selling tours Slider Section ***** -->
  <section class="c-section">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h2 class="heading">Best Selling Tours</h2>

          <!-- *** Card Slider *** -->
          <div class="owl-carousel c-slider oddEven js-trendingTours">
            <?php foreach ($popularPackages as $package) :
              $pricing = ($package['tariff']['cadult'])
                ?  $themeData->convertCurrency($package['tariff']['cadult'], $currency)  : '0.00';
            ?>
              <!-- Card -->
              <div class="card c-card" title="<?php echo $package['package_name'];  ?>">
                <div class="card-image">
                  <img src="<?php echo $package['main_img_url']; ?>" alt="<?php echo $package['package_name']; ?>" />
                  <span class="title text-uppercase"><?php echo $package['tour_type']; ?></span>
                </div>
                <div class="card-body">
                  <h5 class="mb-2 fs-6 color-primary fw-bolder">
                    <?php
                    if ((strlen($package['package_name']) > 25))
                      echo substr($package['package_name'], 0, length: 25) . "...";
                    else
                      echo $package['package_name'];
                    ?>
                  </h5>
                  <span class="d-block mb-3 fs-7 color-primary">
                    <?php echo $package['total_nights']; ?> nights & <?php echo $package['total_days']; ?> days
                  </span>
                  <span class="fs-6 text-secondary d-block">
                    Price Per Person
                  </span>
                  <div class="d-flex align-items-center gap-3 mb-1">
                    <span class="fs-5 fw-bold text-dark"><?php echo $pricing; ?>
                      <sup class="fs-6 text-secondary">*</sup></span>
                  </div>
                  <span class="d-block mb-2 fw-medium fs-7 color-secondary"><i class="fa-solid fa-location-dot me-1"></i> <?php echo $package['destination']['dest_name']; ?></span>
                  <a class="c-button btn fs-7" href="<?php echo BASE_URL_B2C; ?><?php echo $package['seo_slug']; ?>">
                    View Details
                  </a>
                </div>
              </div>
              <!-- Card End -->
            <?php endforeach; ?>
          </div>
          <!-- *** Card Slider End *** -->
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Trending Tours Slider Section End ***** -->

<!-- ***** World class hotels Slider Section ***** -->
<?php
$recommendedHotels = $themeData->getPopularHotels();
if ($recommendedHotels && count($recommendedHotels) > 0) {
?>
  <section class="c-section">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h2 class="heading">World Class Hotels</h2>
        </div>
      </div> <!-- end row -->

      <!-- *** Swiper Gallery *** -->
      <div class="row justify-content-center">
        <div class="col-11">
          <div class="swiper js-swiperGallery">
            <div class="swiper-wrapper">
              <?php foreach ($recommendedHotels as $hotel):
                $starValue = 0;
                if (preg_match("#Star#", $hotel['rating_star'])) {
                  list($starValue,) = explode("Star", $hotel['rating_star']);
                  $starValue = trim($starValue);
                }

                $ratingStars = str_repeat('<i class="fa-solid fa-star fs-10 text-warning" style="text-shadow: 0 0 3px #000;"></i>', $starValue);
                $pricing = $hotel['double_bed'] ? $themeData->convertCurrency($hotel['double_bed'], $currency) : '0.00';
              ?>
                <div class="singleItemSlider swiper-slide">
                  <div class="row align-items-center">
                    <div class="col-md-5 col-sm-12 order-2 order-md-1">
                      <h5 class="mb-2 fs-3 font-family-secondary text-white fw-bolder">
                        <?= htmlspecialchars($hotel['hotel_name']); ?>
                      </h5>

                      <span class="mb-4 fs-6 text-white d-block">
                        <i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($hotel['hotel_address']); ?>
                      </span>

                      <span class="mb-2 fs-7 text-white d-block fw-bolder">
                        <i
                          class="fa-regular fa-circle-check text-white me-2"></i>
                        Deluxe Room with complimentary breakfast
                      </span>
                      <span class="mb-2 fs-7 text-white d-block fw-bolder">
                        <i
                          class="fa-regular fa-circle-check text-white me-2"></i>
                        Free cancellations
                      </span>
                      <span class="mb-5 fs-7 text-white d-block fw-bolder">
                        <i
                          class="fa-regular fa-circle-check text-white me-2"></i>
                        No prepayment needed
                      </span>

                      <div class="itinerary-wrapper is-clean mb-4">
                        <div class="d-flex flex-row gap-2">
                          <?php
                          if (!empty($hotel['amenities'])) {
                            $items = explode(",", $hotel['amenities']);
                            if ($items[0] != '') { ?>
                              <script>
                                setTimeout(function() {
                                  var ameities = getObjectsData(amenities, 'name', '<?php echo $items[0]; ?>');
                                  document.getElementById("amenity1<?= $hotel['hotel_id']; ?>").src = 'cms/Tours_B2B/images/amenities/' + ameities[0]['image'];
                                }, 5000);
                              </script>
                              <div class="itinerary type-2 text-center d-flex flex-column gap-2">
                                <div class="type-2 text-center d-flex flex-column gap-2">
                                  <div class="icon mb-2">
                                    <img id='amenity1<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                                  </div>
                                  <span class="fs-7 fw-medium text-white">
                                    <?php echo $items[0]; ?>
                                  </span>
                                </div>
                              </div>
                              <script>
                                setTimeout(function() {
                                  var ameities2 = getObjectsData(amenities, 'name', '<?php echo $items[1]; ?>');
                                  document.getElementById("amenity2<?= $hotel['hotel_id']; ?>").src = 'cms/Tours_B2B/images/amenities/' + ameities2[0]['image'];
                                }, 5000);
                              </script>
                            <?php }
                            if ($items[1] != '') { ?>
                              <div class="itinerary type-2 text-center d-flex flex-column gap-2">
                                <div class="type-2 text-center d-flex flex-column gap-2">
                                  <div class="icon mb-2">
                                    <img id='amenity2<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                                  </div>
                                  <span class="fs-7 fw-medium text-white">
                                    <?php echo $items[1]; ?>
                                  </span>
                                </div>
                              </div>
                              <script>
                                setTimeout(function() {
                                  var ameities3 = getObjectsData(amenities, 'name', '<?php echo $items[2]; ?>');
                                  document.getElementById("amenity3<?= $hotel['hotel_id']; ?>").src = 'cms/Tours_B2B/images/amenities/' + ameities3[0]['image'];
                                }, 5000);
                              </script>
                            <?php }
                            if ($items[2] != '') { ?>
                              <div class="itinerary type-2 text-center d-flex flex-column gap-2">
                                <div class="type-2 text-center d-flex flex-column gap-2">
                                  <div class="icon mb-2">
                                    <img id='amenity3<?= $hotel['hotel_id']; ?>' alt="" width='32' height='32' />
                                  </div>
                                  <span class="fs-7 fw-medium text-white">
                                    <?php echo $items[2]; ?>
                                  </span>
                                </div>
                              </div>
                            <?php }
                          } else { ?>
                            <div
                              class="itinerary type-2 text-center d-flex flex-column gap-2">
                              <div class="icon mb-2">
                                <i class="fa-solid fa-wifi"></i>
                              </div>
                              <span class="fs-7 fw-medium text-white">
                                Free Wifi
                              </span>
                            </div>

                            <div
                              class="itinerary type-2 text-center d-flex flex-column gap-2">
                              <div class="icon mb-2">
                                <i class="fa-solid fa-water-ladder"></i>
                              </div>
                              <span class="fs-7 fw-medium text-white">
                                Swimming Pool
                              </span>
                            </div>

                            <div
                              class="itinerary type-2 text-center d-flex flex-column gap-2">
                              <div class="icon mb-2">
                                <i class="fa-solid fa-utensils"></i>
                              </div>
                              <span class="fs-7 fw-medium text-white">
                                Breakfast
                              </span>
                            </div>
                          <?php } ?>
                        </div>
                      </div>

                      <span class="fs-6 text-white">Room cost</span>

                      <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="fs-2 text-white fw-bold"><?= $pricing; ?></span>
                      </div>

                      <a class="c-button btn is-white small sm fw-medium fs-8"
                        target="_blank"
                        onclick="get_hotel_listing_page('<?= $hotel['hotel_id']; ?>')">
                        View Details
                      </a>
                    </div> <!-- end col-md-5 -->

                    <div class="col-md-7 col-sm-12 order-1 order-md-2 mb-3 mb-md-0">
                      <?php $hotelImg = ($hotel['main_img']) ? htmlspecialchars($hotel['main_img']) : BASE_URL_B2C . '/images/hotel-single.webp';
                      ?>
                      <img src="<?= $hotelImg; ?>"
                        class="img-fluid"
                        alt="<?= htmlspecialchars($hotel['hotel_type'] ?: $hotel['hotel_name']); ?>" />
                    </div> <!-- end col-md-7 -->
                  </div> <!-- end row -->
                </div> <!-- end swiper-slide -->
              <?php endforeach; ?>
            </div> <!-- end swiper-wrapper -->

            <!-- Swiper Navigation -->
            <div class="swiper-navigation">
              <div class="navigation swiper-button-next">
                <i class="fa-solid fa-arrow-right-long"></i>
              </div>
              <div class="navigation swiper-button-prev">
                <i class="fa-solid fa-arrow-left-long"></i>
              </div>
            </div> <!-- end swiper-navigation -->
          </div> <!-- end swiper -->
        </div> <!-- end col-11 -->
      </div> <!-- end row justify-content-center -->
      <!-- *** Swiper Gallery End *** -->
    </div> <!-- end container -->
  </section>
<?php } ?>

<!-- ***** World class hotels Slider Section End ***** -->

<!-- ***** About us Section ***** -->
<section class="c-section ourStory">
  <div class="container-lg">
    <div class="row gx-md-5">
      <div class="col-md-6 col-sm-12 mb-4 mb-md-0">
        <img
          src="./images/about-us-pic.webp"
          alt="about Us"
          class="about-image" />
      </div>
      <div class="col-md-6 col-sm-12">
        <h3
          class="fs-3 fw-bolder font-family-secondary d-block color-primary c-heading with-border">
          About Us
        </h3>
        <p class="fs-6 d-block mb-3 lh-md">
          At <?php echo $app_name; ?>, we take pride in crafting unforgettable travel experiences. Our customer's testimonials reflect the seamless journeys, personalized service, and incredible destinations we offer.
        </p>
        <a class="c-button btn fs-7 mb-4" href="<?= BASE_URL_B2C ?>about.php">
          View Details
        </a>

        <div class="itinerary-wrapper">
          <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-12">
              <div
                class="itinerary text-center d-flex flex-column gap-2 mb-5 mb-md-0">
                <div class="icon mb-2">
                  <i class="fa-solid fa-location-dot"></i>
                </div>
                <span class="fs-7 fw-medium text-white">550+ <br />
                  destinations</span>
              </div>
            </div>
            <div class="col-md-3 col-sm-4 col-sm-12">
              <div
                class="itinerary text-center d-flex flex-column gap-2 mb-5 mb-md-0">
                <div class="icon mb-2">
                  <i class="fa-solid fa-wallet"></i>
                </div>
                <span class="fs-7 fw-medium text-white">Best Price guaranteed</span>
              </div>
            </div>
            <div class="col-md-3 col-sm-4 col-sm-12">
              <div
                class="itinerary text-center d-flex flex-column gap-2 mb-5 mb-md-0">
                <div class="icon mb-2">
                  <i class="fa-solid fa-award"></i>
                </div>
                <span class="fs-7 fw-medium text-white">Top quality experience</span>
              </div>
            </div>
            <div class="col-md-3 col-sm-4 col-sm-12">
              <div class="itinerary text-center d-flex flex-column gap-2">
                <div class="icon mb-2">
                  <i class="fa-regular fa-comments"></i>
                </div>
                <span class="fs-7 fw-medium text-white">Customer <br />
                  Support</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** About us Section ***** -->
<?php
$testimonials = $themeData->getCustomerTestimonials(10);
?>
<!-- ***** Happy Customers Slider Section ***** -->
<section class="c-section">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <h2 class="heading">Happy Customers</h2>
        <!-- *** Customers Card Slider *** -->
        <div class="owl-carousel c-slider js-testimonials">
          <!-- Card -->
          <?php if ($testimonials && count($testimonials) > 0):
            foreach ($testimonials as $testimonial) { ?>
              <div class="c-customerCard">
                <div class="card-wrapper">
                  <div class="card-wrapper-head">
                    <?php $cleanPath = str_replace('../../../', '/', $testimonial['image']); ?>
                    <img
                      src="<?= 'cms/' . $cleanPath; ?>"
                      alt="photo"
                      height="150"
                      width="150" />

                    <div class="info">
                      <span
                        class="fs-6 fw-bolder text-uppercase text-white d-block text-center mb-1">
                        <?= $testimonial['name']; ?>
                      </span>
                      <span
                        class="fs-8 text-uppercase text-white d-block text-center">
                        <?= $testimonial['designation']; ?>
                      </span>
                    </div>
                  </div>
                  <div class="card-wrapper-body">
                    <p class="fs-7 text-ellipsis-4 lh-lg mb-0">
                      <a class="text-black text-decoration-none" href="<?= BASE_URL_B2C ?>testimonials.php">
                        <?php
                        if ((strlen($testimonial['testm']) > 345))
                          echo substr($testimonial['testm'], 0, length: 345) . "...";
                        else
                          echo $testimonial['testm'];
                        ?>
                      </a>
                    </p>
                  </div>
                </div>
              </div>
              <!-- Card End -->
          <?php }
          endif; ?>
        </div>
        <!-- *** Customers Card Slider End *** -->
      </div>
    </div>
  </div>
</section>
<!-- ***** Happy Customers Slider Section End ***** -->

<?php
$excitingGroupTours = $themeData->getPopularGroupTours();
if ($excitingGroupTours && count($excitingGroupTours) > 0) {
?>
  <!-- ***** Season’s special tours Slider Section ***** -->
  <section class="c-section overlayRight">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h2 class="heading left">Group Tours Special</h2>
        </div>
      </div>
      <div class="row">
        <div class="col-md-11 col-sm-12">
          <!-- *** Hotel Slider single  *** -->
          <div class="container-lg">
            <div class="owl-carousel c-slider js-groupTours">
              <!-- Card -->
              <?php
              $counter = 0;
              foreach ($excitingGroupTours as $tour) {
                $pricing =  $tour['adult_cost'] ? $themeData->convertCurrency($tour['adult_cost'], $currency) : '0.00';
                if ($counter % 2 === 0) echo '<div class="item">'; // open group
              ?>

                <div class="c-seasonCard mb-4">
                  <div class="row align-items-center">
                    <div class="col-md-4 p-md-0">
                      <img src="<?= $tour['image_url']; ?>" class="image" alt="<?= $tour['tour_name']; ?>" />
                    </div>
                    <div class="col-md-5">
                      <div class="p-3">
                        <h5
                          class="fs-4 fw-bolder d-block color-primary mb-1" title="<?= $tour['tour_name']; ?>">
                          <?php
                          echo (strlen($tour['tour_name']) > 30)
                            ? substr($tour['tour_name'], 0, 30) . "..."
                            : $tour['tour_name'];
                          ?>
                        </h5>
                        <span class="fs-7 d-block mb-2 color-primary mb-2">
                          <?php if (strpos($tour['total_nights'], '|') !== false) { ?>

                            <i class="fa-solid fa-bed"></i>
                            <?php
                            $totalNight = explode('|', $tour['total_nights']);
                            $cityName = explode('|', $tour['city_name']);
                            $hotelName = explode('|', $tour['hotel_name']);
                            $total = count($totalNight);
                            $index = 0;
                            foreach ($totalNight as $key => $nt) { ?>
                              <span class="color-primary fs-7"><?= $nt ?> N <?= $cityName[$key]; ?> </span><?= (++$index < $total) ? ' &bull; ' : '' ?>
                            <?php }
                          } elseif (!empty($tour['total_nights'])) { ?>
                            <i class="fa-solid fa-bed"></i>
                            <span class="color-primary fs-7"><?= $tour['total_nights'] ?> N <?= $tour['city_name'] ?></span>
                          <?php } else {
                          } ?>
                        </span>
                        <span class="fs-7 mb-4 text-ellipsis-3"><?= $tour['tour_note']; ?></span>
                        <div class="d-flex align-items-center gap-2">
                          <span class="fs-8 color-primary">
                            Price Per Person
                          </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                          <span class="fs-5 fw-bold text-dark"><?= $pricing; ?></span>
                        </div>
                        <a class="c-button btn fs-7" href='<?php echo BASE_URL_B2C; ?><?php echo 'group-tour/' . $tour['seo_slug']; ?>'>
                          View Details
                        </a>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="row row-cols-2">
                        <div class="col">
                          <div
                            class="itinerary type-2 xs dark text-center d-flex flex-column gap-1 mb-3">
                            <div class="icon mb-1">
                              <i class="fa-solid fa-hotel"></i>
                            </div>
                            <span class="fs-8 fw-medium color-primary fw-bolder">
                              Hotel
                            </span>
                          </div>
                        </div>
                        <div class="col">
                          <div
                            class="itinerary type-2 xs dark text-center d-flex flex-column gap-1 mb-3">
                            <div class="icon mb-1">
                              <i class="fa-solid fa-plane"></i>
                            </div>
                            <span class="fs-8 fw-medium color-primary fw-bolder">
                              Flight
                            </span>
                          </div>
                        </div>
                        <div class="col">
                          <div
                            class="itinerary type-2 xs dark text-center d-flex flex-column gap-1">
                            <div class="icon mb-1">
                              <i class="fa-solid fa-person-hiking"></i>
                            </div>
                            <span class="fs-8 fw-medium color-primary fw-bolder">
                              Activity
                            </span>
                          </div>
                        </div>
                        <div class="col">
                          <div
                            class="itinerary type-2 xs dark text-center d-flex flex-column gap-1">
                            <div class="icon mb-1">
                              <i class="fa-solid fa-car"></i>
                            </div>
                            <span class="fs-8 fw-medium color-primary fw-bolder">
                              Transfer
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php if ($counter % 2 === 1) echo '</div>'; // close group
                $counter++;
              }

              // If odd number of items, close last item manually
              if ($counter % 2 !== 0) echo '
                </div>'; ?>
              <!-- Card End -->
            </div>
          </div>
          <!-- *** Hotel Slider single End  *** -->
        </div>
      </div>
    </div>
  </section>
  <!-- ***** Season’s special tours Slider Section End ***** -->
<?php } ?>
<!-- ***** Our Partners Section ***** -->
<?php
$partners = $themeData->getPartners();
if (count($partners) > 0) {
?>
  <section class="c-section is-filled ourPartner">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-12">
          <h2 class="heading">Our Partners</h2>
        </div>
      </div>
      <div class="row align-items-center">
        <div class="col-12">
          <div
            class="owl-carousel c-slider js-partnerCardSlider partnerCardSlider">
            <!-- Card -->
            <?php
            foreach ($partners as $partner) {
            ?>
              <div class="c-partnerCard">
                <div class="card-image">
                  <img
                    src="<?php echo $partner; ?>"
                    alt="photo"
                    width="60" />
                </div>
              </div>
            <?php }
            ?>
            <!-- Card End -->
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
}
?>
<!-- ***** Our Partners Section End ***** -->

<!-- ***** Blog Articles Slider Section ***** -->
<?php
$blogs = $themeData->getBlogsData(3);
?>
<section class="c-section type-2 blogArticle">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <h2 class="heading">Blog Articles</h2>
      </div>

      <!-- *** Blog Slider  *** -->
      <div class="justify-content-center row">
        <div class="col-sm-12">
          <div class="owl-carousel c-slider js-blogSlider">
            <!-- Card -->
            <?php foreach ($blogs as $blog) { ?>
              <div class="card c-card">
                <div class="card-image">
                  <img src="<?php echo BASE_URL . $blog['image_path']; ?>" alt="..." />
                </div>
                <div class="card-body p-4">
                  <h5
                    class="mb-2 fs-6 font-family-secondary color-primary fw-bolder" title="<?= $blog['title'] ?>">
                    <?php
                    if ((strlen($blog['title']) > 40))
                      echo substr($blog['title'], 0, length: 40) . "...";
                    else
                      echo $blog['title'];
                    ?>
                  </h5>
                  <p class="mb-3 fs-7 text-secondary text-ellipsis-3 lh-md">
                    <?php echo substr($blog['description'], 0, 200) . "..."; ?>
                  </p>
                  <a href="<?= BASE_URL_B2C ?>single-blog.php?blog_id=<?= $blog['id'] ?>" class="c-button btn">
                    Read More
                  </a>
                </div>
              </div>
            <?php } ?>
            <!-- Card End -->
          </div>
        </div>
      </div>
      <!-- *** Blog Slider End  *** -->
    </div>
  </div>
</section>
<!-- ***** Blog Articles Slider Section End ***** -->

<?php $team_array = $themeData->getTeams(5);
if (count($team_array) > 0) {
?>
  <!-- ***** Our Team Slider Section ***** -->
  <section class="c-section type-2 blogArticle d-none d-md-block">
    <div class="container-lg">
      <div class="row">
        <div class="col-md-3 col-sm-12">
          <h2 class="c-heading with-border text-md-start">Our Team</h2>
        </div>

        <div class="col-md-9 col-sm-12">
          <div class="c-imageSlider">
            <?php
            $i = 0;
            foreach ($team_array as $team) {
              $i++;
              if ($team['image']) {
                $cleanPath = str_replace('../../../', '/', $team['image']);
                $cleanPath = "cms/" . $cleanPath;
              } else {
                $cleanPath = './images/user_photo_1.webp';
              } ?>
              <div
                class="imageSlide"
                style="background-image: url('<?php echo $cleanPath; ?>')">
                <div class="slideData">
                  <div class="slideData_count">#<?php echo $i; ?></div>
                  <div class="slideData_info">
                    <span class="fs-6 fw-bold d-block"> <?php echo $team['tname']; ?> </span>
                    <span class="fs-8 fw-medium d-block mb-0">
                      <?= $team['designation']; ?>
                    </span>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php } ?>
<!-- ***** Our Team Slider Section End ***** -->

<!-- ***** Our Gallery Slider Section ***** -->
<?php $gallary_array = $moduleData->getGalleryImages(); ?>
<section class="c-section ourGallery">
  <div class="container-lg">
    <div class="row align-items-center">
      <div class="col-12">
        <h2 class="heading">Our Gallery</h2>
      </div>
    </div>
    <div class="owl-carousel c-slider type-2 js-gallerySlider">
      <?php
      foreach ($gallary_array as $item) {
      ?>
        <div class="card-image">
          <a href="<?= $item['image_url'] ?>" id="lightGalleryImage" class="light-gallery-item" title="<?= $item['dest_name']; ?>">
            <img src="<?= $item['image_url'] ?>" alt="photo" class="img-fluid" style="height: 168px;;" title="<?= $item['dest_name']; ?>" />
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>
<!-- ***** Our Gallery Slider Section End ***** -->

<!-- ***** Our Services Section ***** -->
<?php
$servicesData = mysqli_fetch_all(mysqlQuery("SELECT services FROM `b2c_settings`"), MYSQLI_ASSOC);
$services = isset($servicesData[0]['services']) ? json_decode($servicesData[0]['services'], true) : [];
?>
<section class="c-section ourServices">
  <div class="container-lg">
    <div class="row c-ourServices">
      <div
        class="col-md-4 col-sm-3 col-xs-12 m-0 p-0 d-flex justify-content-center">
        <img src="./images/our-services-tourist.webp" alt="'..." />
      </div>
      <div class="col-md-8 col-sm-9 col-xs-12">
        <div class="services">
          <h3
            class="fs-3 fw-bolder font-family-secondary d-block text-white c-heading with-border mb-4">
            Our Services
          </h3>
          <div class="d-flex flex-wrap mb-5 gap-2 gap-md-5">
            <?php
            if (!empty($services)) :
              foreach ($services as $service) :
                if ($service['service_name'] == 'Airport Transfers')
                  $icon = '<i class="fa fa-plane"></i>';
                else if ($service['service_name'] == 'Adventure Activities')
                  $icon = '<i class="fa fa-hiking"></i>';
                else if ($service['service_name'] == 'Luxury Cruise Tours')
                  $icon = '<i class="fa fa-anchor"></i>';
                else if ($service['service_name'] == 'City Sightseeing Tours')
                  $icon = '<i class="fa fa-city"></i>';
                else if ($service['service_name'] == 'Corporate Travel Services')
                  $icon = '<i class="fa fa-briefcase"></i>';
                else if ($service['service_name'] == 'Hotel Bookings')
                  $icon = '<i class="fa fa-hotel"></i>';
                else if ($service['service_name'] == 'Flight Reservations')
                  $icon = '<i class="fa fa-plane"></i>';
                else if ($service['service_name'] == 'Visa Assistance')
                  $icon = '<i class="fa fa-passport"></i>';
                else if ($service['service_name'] == 'Cruise Holidays')
                  $icon = '<i class="fa fa-ship"></i>';
                else if ($service['service_name'] == 'Travel Insurance')
                  $icon = '<i class="fa fa-shield"></i>';
                else if ($service['service_name'] == 'Adventure Activities')
                  $icon = '<i class="fa fa-mountain"></i>';
                else
                  $icon = '<i class="fa fa-headphones"></i>';
            ?>
                <div class="mb-3 mb-md-0">
                  <div
                    class="itinerary type-3 text-center d-flex flex-column gap-2">
                    <div class="icon mb-2">
                      <?php echo $icon; ?>
                    </div>
                    <span class="fs-7 fw-medium text-white">
                      <?= str_replace(' ', '</br>', $service['service_name']) ?>
                    </span>
                  </div>
                </div>
              <?php
              endforeach;
            else :
              ?>
              <p class="text-center font-weight-bold text-danger">No services available</p>
            <?php
            endif;
            ?>
          </div>
          <a class="c-button btn is-white" href="<?= BASE_URL_B2C ?>services.php">
            View Details
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Our Services Section End ***** -->

<!-- ***** Counter Section Section ***** -->
<section class="c-section trigger-section">
  <div class="container-lg">
    <div class="row align-items-center justify-content-center">
      <div class="col-12">
        <h2 class="heading">Our Expertise</h2>
      </div>
      <div class="col-10 col-sm-12">
        <div class="row g-5">
          <!-- Card -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="counter text-center mb-3 mb-md-0">
              <div class="counter-wrapper">
                <h5 class="d-block fs-1 font-family-secondary fw-bold mb-3">
                  <span class="js-number-counter" data-target="1000">
                    0
                  </span>
                  +
                </h5>
                <span class="d-block fs-5 font-family-secondary">Awesome hikers</span>
              </div>
            </div>
          </div>
          <!-- Card End -->

          <!-- Card -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="counter text-center mb-3 mb-md-0">
              <div class="counter-wrapper">
                <h5 class="d-block fs-1 font-family-secondary fw-bold mb-3">
                  <span class="js-number-counter" data-target="80"> 0 </span>+
                </h5>
                <span class="d-block fs-5 font-family-secondary">Stunning destinations</span>
              </div>
            </div>
          </div>
          <!-- Card End -->

          <!-- Card -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="counter text-center mb-3 mb-md-0">
              <div class="counter-wrapper">
                <h5 class="d-block fs-1 font-family-secondary fw-bold mb-3">
                  <span class="js-number-counter" data-target="1200">
                    0 </span>+
                </h5>
                <span class="d-block fs-5 font-family-secondary">Miles to hike</span>
              </div>
            </div>
          </div>
          <!-- Card End -->

          <!-- Card -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="counter text-center mb-3 mb-md-0">
              <div class="counter-wrapper">
                <h5 class="d-block fs-1 font-family-secondary fw-bold mb-3">
                  <span class="js-number-counter" data-target="15"> 0 </span>+
                </h5>
                <span class="d-block fs-5 font-family-secondary">Years in service</span>
              </div>
            </div>
          </div>
          <!-- Card End -->
        </div>
      </div>
    </div>
  </div>
</section>
<!-- ***** Counter Section End ***** -->

<!-- ***** Write to us Section ***** -->
<section class="c-section bg-color-primary p-0 mb-0">
  <div class="container-lg">
    <div class="row">
      <div class="col-md-6 col-sm-12 order-md-1 order-2 p-0">
        <form id="contactForm">
          <div class="bg-color-primary p-5">
            <h3
              class="fs-3 fw-bolder font-family-secondary d-block text-white c-heading with-border mb-4">
              Contact Us
            </h3>
            <div class="row">
              <div class="col-sm-6 col-xs-12 mb-3">
                <input
                  type="text"
                  class="form-control c-input is-clean" name="name"
                  placeholder="Name *" required />
              </div>
              <div class="col-sm-6 col-xs-12 mb-3">
                <input
                  type="email"
                  class="form-control c-input is-clean" name="email"
                  placeholder="Email *" required />
              </div>
              <div class="col-sm-6 col-xs-12 mb-3">
                <input
                  type="text"
                  class="form-control c-input is-clean" name="phone"
                  placeholder="Phone Number *" required />
              </div>
              <div class="col-12 mb-4">
                <textarea
                  class="form-control c-input is-clean" name="message"
                  placeholder="Message *"
                  rows="3" required></textarea>
              </div>
            </div>
            <div class="text-end">
              <button class="c-button btn is-white">Submit</button>
            </div>
            <div id="response" class="mt-3"></div>
          </div>
        </form>
      </div>
      <div class="col-md-6 col-sm-12 order-md-2 order-1 mb-4 mb-md-0 p-0">
        <img
          src="./images/contact-us.webp"
          alt="..."
          style="width: 100%" />
      </div>
    </div>
  </div>
  <?php $googleMapScript = $moduleData->getB2cSettings('google_map_script'); ?>
  <div class="gMap">
    <?php if ($googleMapScript != '') { ?>
      <iframe
        src="<?= $googleMapScript ?>"
        style="border: 0"
        class="map"
        allowfullscreen=""
        referrerpolicy="no-referrer-when-downgrade"
        loading="lazy">
      </iframe>
    <?php } ?>
  </div>
</section>
<!-- ***** Write to us Section End ***** -->

<!-- ***** Flight :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="attendantModal"
  tabindex="-1"
  aria-labelledby="attendantModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-fullscreen-sm-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Travellers Information</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">Adults (12y +)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="adult" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">CHILDREN (2y - 12y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="child" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">INFANTS (below 2y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10"
            data-x-input="infant" />
        </div>

        <span class="fs-7 fw-medium d-block text-uppercase">CHOOSE TRAVEL CLASS
        </span>
        <div class="d-flex flex-row mb-3">
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="economyClass"
              value="Economy"
              checked
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="economyClass">
              Economy
            </label>
          </div>
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="premiumClass"
              value="Premium Economy"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="premiumClass">
              Premium Economy
            </label>
          </div>
        </div>
        <div class="d-flex flex-row mb-3">

          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="businessClass"
              value="Business"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="businessClass">
              Business
            </label>
          </div>

          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="firstClass"
              value="First"
              data-x-input="travelClass" />
            <label class="form-check-label fs-7" for="firstClass">
              First Class
            </label>
          </div>
        </div>
        <div class="text-center">
          <button class="btn c-button btn-lg" onclick="attendantModalUpdater()">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Flight :: Traveller information Modal End ***** -->
</div>

<?php
include 'layouts/footer.php'; // Include footer
include 'buy_now.php';
?>

<script type="text/javascript" src="js/scripts.js"></script>
<script type="text/javascript" src="view/transfer/js/index.js"></script>
<script type="text/javascript" src="view/activities/js/index.js"></script>
<script type="text/javascript" src="view/tours/js/index.js"></script>
<script type="text/javascript" src="view/group_tours/js/index.js"></script>
<script type="text/javascript" src="view/hotel/js/index.js"></script>
<script type="text/javascript" src="view/hotel/js/amenities.js"></script>
<script type="text/javascript" src="view/flight/js/index.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/thumbnail/lg-thumbnail.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.min.js"></script>

<script>
  var service = '<?php echo $service; ?>';

  if (service && (service !== '' || service !== undefined)) {

    var checkLink = $('.c-searchContainer .c-search-tabs li');

    var checkTab = $('.c-searchContainer .search-tab-content .tab-pane');

    checkLink.each(function() {

      var child = $(this).children('.nav-link');

      if (child.data('service') === service) {

        $(this).siblings().children('.nav-link').removeClass('active');

        child.addClass('active');

      }

    });

    checkTab.each(function() {

      if ($(this).data('service') === service) {

        $(this).addClass('active show').siblings().removeClass('active show');

      }

    })

  }

  //Get Amenities by mathcing name
  function getObjectsData(obj, key, val) {
    var objects = [];
    for (var i in obj) {
      if (!obj.hasOwnProperty(i)) continue;
      if (typeof obj[i] == 'object') {
        objects = objects.concat(getObjectsData(obj[i], key, val));
      } else if ((i == key && obj[i] == val) || (i == key && val == '')) {
        //if key matches and value matches or if key matches and value is not passed (eliminating the case where key matches but passed value does not)
        objects.push(obj);
      } else if (obj[i] == val && key == '') {
        //only add if the object is not already in the array
        if (objects.lastIndexOf(obj) == -1) {
          objects.push(obj);
        }
      }
    }
    return objects;
  }

  function filterSearch() {
    var input, filter, found, table, tr, td, i, j;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td");
      for (j = 0; j < td.length; j++) {
        if (td[j].innerHTML.toUpperCase().indexOf(filter) > -1) {
          found = true;
        }
      }
      if (found) {
        tr[i].style.display = "";
        found = false;
      } else {
        tr[i].style.display = "none";
      }
    }
  }

  $(function() {
    $('#enq_form').validate({
      rules: {},
      submitHandler: function(form) {

        $('#enq_submit').prop('disabled', 'true');
        var base_url = $('#base_url').val();
        var crm_base_url = $('#crm_base_url').val();
        var name = $('#name').val();
        var phone_no = $('#phone_no').val();
        var email = $('#email').val();
        var city = $('#city').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var service_name = $('#service_name').val();
        document.getElementById('enq_submit').textContent = 'Loading';

        $.ajax({
          type: 'post',
          url: crm_base_url + "controller/b2c_settings/b2c/homepage_enq.php",
          data: {
            name: name,
            phone_no: phone_no,
            email: email,
            city: city,
            from_date: from_date,
            to_date: to_date,
            service_name: service_name
          },
          success: function(result) {
            var msg = 'Thank you for enquiry with us. Our experts will contact you shortly.';
            $.alert({
              title: 'Notification!',
              content: msg,
            });

            document.getElementById('enq_submit').textContent = 'Enquire Now';
            setTimeout(() => {
              window.location.href = base_url;
            }, 2000);
          }
        });
      }
    });
  });

  window.addEventListener('scroll', function() {
    const header = document.getElementById('top-header');

    if (window.scrollY > 50) { // Adjust the scroll position where you want it to stick
      header.classList.add('sticky');
    } else {
      header.classList.remove('sticky');
    }
  });

  $(document).ready(function() {
    lightGallery(document.getElementById('lightGalleryImage'), {
      plugins: [lgZoom, lgThumbnail],
      speed: 500,
      download: true,
    });

    setTimeout(function() {
      var width = $(".light-gallery-item img").width();
      console.log(width);
      $(".light-gallery-item img").height(width);
    }, 1000);

    jQuery.validator.addMethod("lettersOnly", function(value, element) {
      return this.optional(element) || /^[a-zA-Z\s]+$/.test(value); // only letters and space
    }, "Please enter letters only.");

    jQuery.validator.addMethod("validMobile", function(value, element) {
      return this.optional(element) || /^[6-9]\d{9}$/.test(value);
    }, "Please enter a valid 10-digit mobile number.");

    $("#contactForm").validate({
      rules: {
        name: {
          required: true,
          lettersOnly: true
        },
        email: {
          required: true,
          email: true
        },
        phone: {
          required: true,
          validMobile: true
        }
      },
      submitHandler: function(form) {
        // This will only run if the form is valid
        $.ajax({
          url: 'layouts/send_mail.php',
          type: 'POST',
          data: $(form).serialize(), // ← this must be used, not JSON
          success: function(response) {
            $('#response').html('<b>Response:</b><br>' + response);
          },
          error: function() {
            $('#response').html('<b style="color:red;">AJAX request failed</b>');
          }
        });
      }
    });

    $('#contactForm').on('submit', function(e) {
      if (!$(this).valid()) {
        e.preventDefault(); // Stop form submission if invalid
      }
    });
  });
</script>