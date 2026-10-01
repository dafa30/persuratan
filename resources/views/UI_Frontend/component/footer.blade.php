<section class="bagian-persuratan" data-aos="fade-up" data-aos-duration="900">
    <div class="container">
        <div class="row align-items-stretch g-4">
            <div class="col-lg-7">
                <div class="map-frame">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.411626382937!2d106.79765117445268!3d-6.209314493778558!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f6b0cb230357%3A0x353122a55532626e!2sMajelis%20Permusyawaratan%20Rakyat%20Republik%20Indonesia!5e0!3m2!1sid!2sid!4v1734342747530!5m2!1sid!2sid" title="Lokasi Majelis Permusyawaratan Rakyat Republik Indonesia" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <span class="map-mode-label"><i class="bi bi-sun-fill" aria-hidden="true"></i> Mode siang</span>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="contact-panel">
                    <span class="contact-eyebrow">Hubungi Kami</span>
                    <h2>BAGIAN PERSURATAN DAN KEARSIPAN</h2>
                    <p>MAJELIS PERMUSYAWARATAN RAKYAT REPUBLIK INDONESIA</p>
                    <div class="contact-details">
                        <p><i class="bi bi-building" aria-hidden="true"></i> Gedung Bharana Graha II lantai 2</p>
                        <p><i class="bi bi-geo-alt" aria-hidden="true"></i> Kompleks MPR/DPR/DPD RI</p>
                        <p><i class="bi bi-pin-map" aria-hidden="true"></i> Jl. Gatot Subroto No. 6 Jakarta Pusat 10270</p>
                        <p><i class="bi bi-telephone" aria-hidden="true"></i> (+62) 21 5789 5078/5079</p>
                        <p><i class="bi bi-envelope" aria-hidden="true"></i> persuratan@setneg.go.id</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const contactSection = document.querySelector('.bagian-persuratan');
        const modeLabel = contactSection?.querySelector('.map-mode-label');

        if (!contactSection || !modeLabel) {
            return;
        }

        const currentHour = new Date().getHours();
        const isNight = currentHour >= 18 || currentHour < 6;
        contactSection.classList.toggle('is-night', isNight);
        modeLabel.innerHTML = isNight
            ? '<i class="bi bi-moon-stars-fill" aria-hidden="true"></i> Mode malam'
            : '<i class="bi bi-sun-fill" aria-hidden="true"></i> Mode siang';
    });
</script>

<!-- Footer -->
<footer class="bg-white text-black py-4">
    <div class="container text-center">
       <b><p class="footer">&copy; {{ date('Y') }} Sekretariat Jenderal MPR RI</p></b>
    </div>
</footer>
