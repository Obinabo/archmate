    </main>

    <footer class="footer2 adm-foot" style="color: var(--body-color);">
        <div class="copyright align-center">
            <p class="white">© 2024 <?php echo SITE_NAME_SHORT; ?> Console — crafted with care. Powered by Walcode.</p>
        </div>
    </footer>

    <script src="../assets/js/aos.js"></script>
    <script>
        var mobileIcon = document.getElementById('mobile-icon');
        var navCont = document.querySelector('.admin-cont');
        if (mobileIcon && navCont) {
            mobileIcon.addEventListener("click", function(e){
                e.preventDefault();
                e.stopPropagation();
                navCont.classList.toggle('show-nav');
                var isNavContVisible = navCont.classList.contains('show-nav');
                if (isNavContVisible) {
                    mobileIcon.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                } else {
                    mobileIcon.innerHTML = '<i class="fa-solid fa-bars"></i>';
                }
            });

            document.body.addEventListener('click', function(e) {
                if (!navCont.classList.contains('show-nav')) {
                    return;
                }
                if (navCont.contains(e.target) || mobileIcon.contains(e.target)) {
                    return;
                }
                navCont.classList.remove('show-nav');
                mobileIcon.innerHTML = '<i class="fa-solid fa-bars"></i>';
            });
        }

        var redButton = document.getElementById("red-button");
        var regTool = document.querySelector('.reg-tool');
        if (redButton && regTool) {
            redButton.addEventListener("click", function(e){
                if(regTool.style.display === 'none' || regTool.style.display === ''){
                    e.preventDefault();
                    regTool.style.display = 'block';
                }else{
                    e.preventDefault();
                    regTool.style.display = 'none';
                }
            });
        }

        var itemLinks = document.getElementById('noClick');
        if (itemLinks) {
            itemLinks.addEventListener('click', function(e){
                e.preventDefault();
            });
        }

        if (window.AOS) {
            AOS.init({ easing: 'ease-in-out-sine', duration: 800, once: true });
        }
    </script>
</body>
</html>
