let navbar = document.querySelector('.header .flex .navbar');
let profile = document.querySelector('.header .flex .profile');

document.querySelector('#menu-btn').onclick = () =>{
   navbar.classList.toggle('active');
   profile.classList.remove('active');
}

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   navbar.classList.remove('active');
}

window.onscroll = () =>{
   navbar.classList.remove('active');
   profile.classList.remove('active');
}

let mainImage = document.querySelector('.quick-view .box .row .image-container .main-image img');
let subImages = document.querySelectorAll('.quick-view .box .row .image-container .sub-image img');

subImages.forEach(images =>{
   images.onclick = () =>{
      src = images.getAttribute('src');
      mainImage.src = src;
   }
});
document.getElementById('search-button').addEventListener('click', function() {
   let searchQuery = document.getElementById('search-input').value;

   // Gửi yêu cầu AJAX đến controller
   fetch(`/admin/products?search=${searchQuery}`)
       .then(response => response.text())
       .then(data => {
           // Cập nhật lại bảng sản phẩm
           document.querySelector('.table-container').innerHTML = data;
       })
       .catch(error => console.error('Có lỗi xảy ra:', error));
});

