// public/assets/js/guest/checkout_location.js
(function () {
  if (typeof window.jQuery === "undefined") return;

  $(document).ready(function () {
    const $province = $("#province");
    const $district = $("#district");
    const $ward = $("#ward");

    if (!$province.length || !$district.length || !$ward.length) return;

    function resetDistrict() {
      $district.empty().append('<option value="">Chọn quận/huyện</option>');
      $district.prop("disabled", true);
    }

    function resetWard() {
      $ward.empty().append('<option value="">Chọn phường/xã</option>');
      $ward.prop("disabled", true);
    }

    function fillSelect($el, placeholder, data, oldVal) {
      $el.empty().append(`<option value="">${placeholder}</option>`);
      $.each(data || [], function (_, item) {
        $el.append($("<option>", { value: item.id, text: item.name }));
      });
      $el.prop("disabled", false);
      if (oldVal) $el.val(String(oldVal));
    }

    // init
    resetDistrict();
    resetWard();

    // Province -> Districts
    $province.on("change", function () {
      const province_id = $(this).val();
      const url = $province.data("district-url") || "/get-districts";

      resetDistrict();
      resetWard();
      if (!province_id) return;

      $.ajax({
        url,
        method: "GET",
        dataType: "json",
        data: { province_id },
        success: function (data) {
          fillSelect($district, "Chọn quận/huyện", data, $district.data("old"));
          // Sau khi set old district xong, trigger để load ward (nếu có old)
          if ($district.val()) $district.trigger("change");
        },
        error: function (xhr) {
          console.log("Load districts failed:", xhr.status, xhr.responseText);
          resetDistrict();
          resetWard();
        },
      });
    });

    // District -> Wards
    $district.on("change", function () {
      const district_id = $(this).val();
      const url = $district.data("ward-url") || "/get-wards";

      resetWard();
      if (!district_id) return;

      $.ajax({
        url,
        method: "GET",
        dataType: "json",
        data: { district_id },
        success: function (data) {
          fillSelect($ward, "Chọn phường/xã", data, $ward.data("old"));
        },
        error: function (xhr) {
          console.log("Load wards failed:", xhr.status, xhr.responseText);
          resetWard();
        },
      });
    });

    // Nếu có old province thì tự load district/ward
    if ($province.val()) $province.trigger("change");
  });
})();
