<!-- ***** Footer Section ***** -->

<button id="scrollToTopBtn" title="Go to top">
  ↑
</button>

<!-- ***** Footer Section ***** -->
<footer class="c-footer">
  <!-- Footer Top -->
  <div class="footer-top">
    <div class="container-lg">
      <div class="row">
        <div class="col-md-3 col-sm-6">
          <div class="sectionBlock mb-4 mb-md-0">
            <div class="mb-2">
              <p
                class="fs-7 font-family-secondary fw-bolder mb-0 text-white text-uppercase pattern">
                Company
              </p>
            </div>
            <a
              href="<?= BASE_URL_B2C ?>about.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              About Us
            </a>
            <a
              href="<?= BASE_URL_B2C ?>award.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Awards
            </a>
            <a
              href="<?= BASE_URL_B2C ?>careers.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Career
            </a>
            <a
              href="<?= BASE_URL_B2C ?>gallery.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Gallery
            </a>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-4 mb-md-0">
            <div class="mb-2">
              <p
                class="fs-7 font-family-secondary fw-bolder mb-0 text-white text-uppercase pattern">
                Support
              </p>
            </div>

            <a
              href="<?= BASE_URL_B2C ?>offers.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Offers
            </a>
            <a
              href="<?= BASE_URL_B2C ?>services.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Services
            </a>
            <a
              href="<?= BASE_URL_B2C ?>testimonials.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Testimonials
            </a>
            <a
              href="<?= BASE_URL_B2C ?>contact.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Contact Us
            </a>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-4 mb-md-0">
            <div class="mb-2">
              <p
                class="fs-7 font-family-secondary fw-bolder mb-0 text-white text-uppercase pattern">
                Other Services
              </p>
            </div>

            <a
              href="<?= BASE_URL_B2C ?>view/activities/activities-listing.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Activities
            </a>
            <a
              href="<?= BASE_URL_B2C ?>view/ferry/ferry-listing.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Cruise
            </a>
            <a
              href="<?= BASE_URL_B2C ?>view/hotel/hotel-listing.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Hotel
            </a>
            <a
              href="<?= BASE_URL_B2C ?>view/visa/visa-listing.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Visa
            </a>
          </div>
        </div>
        <div class="col-md-2 col-sm-6">
          <div class="sectionBlock mb-4 mb-md-0">
            <div class="mb-2">
              <p
                class="fs-7 font-family-secondary fw-bolder mb-0 text-white text-uppercase pattern">
                Important Links
              </p>
            </div>

            <a
              href="<?= BASE_URL_B2C ?>terms-conditions.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Terms Of Use
            </a>
            <a
              href="<?= BASE_URL_B2C ?>privacy-policy.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Privacy Policy
            </a>
            <a
              href="<?= BASE_URL_B2C ?>cancellation-policy.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Cancellation Policy
            </a>
            <a
              href="<?= BASE_URL_B2C ?>refund-policy.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Refund Policy
            </a>
            <a
              href="<?= BASE_URL_B2C ?>blog.php"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              Travel Blog
            </a>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="sectionBlock mb-4 mb-md-0">
            <div class="mb-2">
              <p
                class="fs-7 font-family-secondary fw-bolder mb-0 text-white text-uppercase pattern">
                Contact Us
              </p>
            </div>

            <a
              href="mailTo:<?php echo $app_email_id_send; ?>"
              class="text-decoration-none d-block fs-8 mb-2 text-white">
              <i class="fa-solid fa-envelope me-2"></i> <?php echo $app_email_id_send; ?>
            </a>
            <a href="tel:<?php echo $app_contact_no; ?>" class="text-decoration-none d-block fs-8 mb-2 text-white">
              <i class="fa-solid fa-phone me-2"></i> <?php echo $app_contact_no; ?>
            </a>
            <span class="text-decoration-none d-block fs-8 mb-2 text-white">
              <i class="fa-solid fa-location-dot me-2"></i> iTours Operator Software
              B Wings, Teerth Technospace, Mumbai, Highway, Baner, Bengaluru, Pune, Maharashtra 411021
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer Top End -->

  <!-- Footer Bottom -->
  <div class="footer-bottom">
    <div class="container-lg">
      <div class="row align-items-center">
        <div class="col-md-6 col-xs-12 order-2 order-md-1">
          <span
            class="fs-7 d-block mb-0 text-md-start text-center text-white">
            Copyright © <?= date('Y'); ?> <?php echo $app_name; ?>. All rights reserved
          </span>
        </div>
        <div
          class="col-md-6 col-xs-12 mb-4 mb-md-0 order-1 order-md-2 text-center text-md-end">
          <div class="d-inline-flex flex-row gap-2">
            <?php foreach ($socialIcons as $icon): ?>
              <?php if ($icon['fb']) { ?>
                <a href="<?php echo $icon['fb']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-facebook-f"></i>
                </a>
              <?php } ?>
              <?php if ($icon['tw']) { ?>
                <a href="<?php echo $icon['tw']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-twitter"></i>
                </a>
              <?php } ?>
              <?php if ($icon['inst']) { ?>
                <a href="<?php echo $icon['inst']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-instagram"></i>
                </a>
              <?php } ?>
              <?php if ($icon['li']) { ?>
                <a href="<?php echo $icon['li']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-linkedin"></i>
                </a>
              <?php } ?>
              <?php if ($icon['wa']) { ?>
                <a href="<?php echo $icon['wa']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-whatsapp"></i>
                </a>
              <?php } ?>
              <?php if ($icon['yu']) { ?>
                <a href="<?php echo $icon['yu']; ?>" class="settingButton transparent" target="_blank">
                  <i class="fa-brands fa-youtube"></i>
                </a>
              <?php } ?>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer Bottom End -->
</footer>
<!-- ***** Footer Section Section ***** -->

<!-- ***** Hotel :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="travellerInformationHotel"
  tabindex="-1"
  aria-labelledby="travellerInformationHotelLabel"
  aria-hidden="true">
  <div class="modal-dialog">
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
          <span class="d-block fs-7 text-secondary mb-1"> Adults </span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1" />
        </div>

        <div class="mb-2">
          <span class="d-block fs-7 text-secondary mb-1"> Children </span>
          <input
            class="form-control c-input transparent mb-2"
            type="number"
            placeholder="0"
            min="0" />
          <div class="d-flex gap-2">
            <div class="flex-grow-1">
              <select class="c-select fs-7">
                <option>0 Years old</option>
                <option>1 Years old</option>
                <option>2 Years old</option>
                <option>3 Years old</option>
              </select>
            </div>
            <div class="flex-grow-1">
              <select class="c-select fs-7">
                <option>0 Years old</option>
                <option>1 Years old</option>
                <option>2 Years old</option>
                <option>3 Years old</option>
              </select>
            </div>
          </div>
        </div>

        <div class="mb-1">
          <span class="d-block fs-7 text-secondary mb-1"> Rooms </span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1" />
        </div>

        <div class="text-center">
          <button class="btn c-button btn-lg" data-bs-dismiss="modal">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Hotel :: Traveller information Modal End ***** -->

<!-- ***** Flight :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="exampleModal"
  tabindex="-1"
  aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
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
            max="10" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">CHILDREN (2y - 12y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">INFANTS (below 2y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10" />
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
              checked />
            <label class="form-check-label fs-7" for="economyClass">
              Economy
            </label>
          </div>
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="firstClass" />
            <label class="form-check-label fs-7" for="firstClass">
              First Class
            </label>
          </div>
          <div class="form-check flex-fill">
            <input
              class="form-check-input"
              type="radio"
              name="travelClass"
              id="businessClass" />
            <label class="form-check-label fs-7" for="businessClass">
              Business
            </label>
          </div>
        </div>
        <div class="text-center">
          <button class="btn c-button btn-lg" data-bs-dismiss="modal">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Flight :: Traveller information Modal End ***** -->

<!-- ***** Group Tour :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="travellerInformationGroupTour"
  tabindex="-1"
  aria-labelledby="travellerInformationGroupTourLabel"
  aria-hidden="true">
  <div class="modal-dialog">
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
            max="10" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">CHILDREN (2y - 12y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10" />
        </div>
        <div class="mb-3">
          <span class="fs-7 fw-medium d-block text-uppercase">INFANTS (below 2y)</span>
          <span class="fs-7 fw-medium text-secondary d-block mb-2">On the day of travel</span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1"
            max="10" />
        </div>

        <div class="text-center">
          <button class="btn c-button btn-lg" data-bs-dismiss="modal">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Group Tour :: Traveller information Modal End ***** -->

<!-- ***** Holiday :: Traveller information Modal ***** -->
<div
  class="modal fade"
  id="travellerInformationHoliday"
  tabindex="-1"
  aria-labelledby="travellerInformationHolidayLabel"
  aria-hidden="true">
  <div class="modal-dialog">
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
          <span class="d-block fs-7 text-secondary mb-1"> Adults </span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1" />
        </div>

        <div class="mb-2">
          <span class="d-block fs-7 text-secondary mb-1"> Children </span>
          <input
            class="form-control c-input transparent mb-2"
            type="number"
            placeholder="0"
            min="0" />
          <div class="d-flex gap-2">
            <div class="flex-grow-1">
              <select class="c-select fs-7">
                <option>0 Years old</option>
                <option>1 Years old</option>
                <option>2 Years old</option>
                <option>3 Years old</option>
              </select>
            </div>
            <div class="flex-grow-1">
              <select class="c-select fs-7">
                <option>0 Years old</option>
                <option>1 Years old</option>
                <option>2 Years old</option>
                <option>3 Years old</option>
              </select>
            </div>
          </div>
        </div>

        <div class="mb-1">
          <span class="d-block fs-7 text-secondary mb-1"> Rooms </span>
          <input
            class="form-control c-input transparent"
            type="number"
            placeholder="1"
            min="1" />
        </div>

        <div class="text-center">
          <button class="btn c-button btn-lg" data-bs-dismiss="modal">
            Add
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- ***** Holiday :: Traveller information Modal End ***** -->
<div id="site_alert"></div>
<script src="./js/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/jquery.validate.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js" integrity="sha512-KFHXdr2oObHKI9w4Hv1XPKc898mE4kgYx58oqsc/JqqdLMDI4YjOLzom+EMlW8HFUd0QfjfAvxSL6sEq/a42fQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="./js/bootstrap.bundle.min.js" async defer></script>
<script src="./js/owl.carousel.min.js"></script>
<script src="./js/swiper-bundle.min.js"></script>
<script type="text/javascript" src="js/jquery.datetimepicker.full.js"></script>
<script src="./js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo BASE_URL_B2C ?>js2/vi.alert.js"></script>

<style>
  .vi_alert_parent .item {
    padding: 0px 0px 78px 19px !important;
  }
</style>
<script>
  if (!sessionStorage.getItem('final_arr')) {
    var initial_array = [];
    initial_array.push({
      rooms: {
        room: 1,
        adults: 2,
        child: 0,
        childAge: []
      }
    });
    sessionStorage.setItem('final_arr', JSON.stringify(initial_array));
  }
  // scroling feature add js code by vidya 

  // बटन को दिखाने के लिए जब यूज़र नीचे स्क्रॉल करता है
  window.onscroll = function() {
    let btn = document.getElementById("scrollToTopBtn");
    if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
      btn.style.display = "block";
    } else {
      btn.style.display = "none";
    }
  };

  // बटन पर क्लिक करने से पेज ऊपर स्क्रॉल हो जाएगा
  document.getElementById("scrollToTopBtn").onclick = function() {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  };

  //Get Amenities by mathcing name
  function getObjectsData(obj, key, val) {
    console.log("sdsd");
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
</script>

<script>
  const metaData = <?php echo json_encode($meta_tags, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>;
  const getPageMeta = (pageName) => {
    return metaData.find(item => item.page === pageName);
  };
  const metas = getPageMeta('<?= $_SESSION['page_type'] ?>');
  console.log(metas);
  if (metas) {
    var meta = document.createElement('meta');

    meta.setAttribute('name', 'keywords');
    meta.setAttribute('content', metas.keywords);
    var meta2 = document.createElement('meta');
    meta2.setAttribute('name', 'description');
    meta2.setAttribute('content', metas.description);
    document.title = metas.title;
    document.getElementsByTagName('head')[0].appendChild(meta);
    document.getElementsByTagName('head')[0].appendChild(meta2);
  }
</script>

</body>

</html>