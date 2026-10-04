@php
    $base = 'https://www.hsb.co.id';
    $downloadUrl = 'https://hsb-investasi.onelink.me/0vzD?pid=main_website';
    $footerGroups = [
        [
            'title' => 'Trading',
            'links' => [
                ['Jenis Akun', "$base/jenis-akun"],
                ['Komisi & Spread', "$base/komisi-dan-spread"],
                ['Swap', "$base/swap"],
                ['Deposit', "$base/deposit"],
                ['Akun Real', "$base/akun-real"],
                ['Akun Demo', "$base/akun-demo"],
                ['Promo', "$base/promo"],
            ],
        ],
        [
            'title' => 'Produk',
            'links' => [
                ['Forex', "$base/forex"],
                ['Komoditas', "$base/komoditas"],
                ['Indeks', "$base/indeks"],
            ],
            'secondTitle' => 'Platforms',
            'secondLinks' => [
                ['MetaTrader 5', "$base/trading-platforms/metatrader-5"],
                ['HSB Aplikasi Trading', "$base/trading-platforms/aplikasi-trading"],
            ],
        ],
        [
            'title' => 'Edukasi',
            'links' => [
                ['HSB Akademi', "$base/akademi"],
                ['Webinar & Events', "$base/event"],
                ['Platform Tutorials', "$base/platform-tutorial"],
                ['Manajemen Risiko', "$base/akademi/manajemen-risiko"],
                ['Pengantar Dasar Trading', "$base/akademi/dasar-trading"],
                ['Video Trading', "$base/akademi/video"],
                ['FAQ', "$base/akademi/faq"],
                ['E-books Trading', "$base/akademi/ebooks"],
                ['Glosarium', "$base/glosarium"],
                ['Blog', 'https://blog.hsb.co.id/'],
                ['Profil', "$base/profil"],
            ],
        ],
        [
            'title' => 'Company',
            'links' => [
                ['About Us', "$base/about"],
                ['Contact Us', "$base/contact"],
                ['Karir', "$base/karir"],
                ['Program Influencer', "$base/influencer"],
                ['Program IB', "$base/program-ib"],
            ],
        ],
    ];
    $socials = [
        ['Instagram', 'instagram.svg', 'https://www.instagram.com/hsb.investasi/'],
        ['TikTok', 'tiktok.webp', 'https://www.tiktok.com/@hsb.investasi'],
        ['YouTube', 'youtube.svg', 'https://www.youtube.com/c/HSBInvestasi'],
        ['Facebook', 'facebook.svg', 'https://www.facebook.com/hsb.investasi'],
        ['Threads', 'threads.svg', 'https://www.threads.com/@hsb.investasi'],
        ['X', 'x.svg', 'https://x.com/hsb_investasi'],
        ['LinkedIn', 'linkedin.svg', 'https://www.linkedin.com/company/hsb-investasi'],
    ];
    $awards = [
        ['2025', 'Best OTC Broker 2025'],
        ['2024', 'Most Innovative Broker 2024'],
        ['2023', 'Most Improved Broker 2023'],
        ['2022', 'Most Innovative Broker 2022'],
        ['2021', 'Most Improved Broker 2021'],
    ];
@endphp

<section class="awards-strip" aria-labelledby="awards-title">
    <div class="container awards-inner">
        <h2 id="awards-title">Memenangkan<br>Berbagai Macam<br>Penghargaan</h2>
        <ul class="award-items" role="list">
            @foreach ($awards as [$year, $title])
                <li><img src="{{ asset("images/hsb-footer/award-$year.webp") }}" alt="{{ $title }}" width="307" height="140" loading="lazy"></li>
            @endforeach
        </ul>
    </div>
</section>

<footer class="footer">
    <div class="container footer-main">
        <div class="footer-columns">
            <div class="footer-links-grid">
                @foreach ($footerGroups as $group)
                    <div class="footer-column">
                        <h3>{{ $group['title'] }}</h3>
                        <div class="footer-link-list">
                            @foreach ($group['links'] as [$label, $href])
                                <a href="{{ $href }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                            @endforeach
                        </div>
                        @isset($group['secondTitle'])
                            <h3 class="footer-subheading">{{ $group['secondTitle'] }}</h3>
                            <div class="footer-link-list">
                                @foreach ($group['secondLinks'] as [$label, $href])
                                    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                                @endforeach
                            </div>
                        @endisset
                    </div>
                @endforeach
            </div>

            <div class="footer-column footer-download">
                <h3>Download</h3>
                <div class="download-assets">
                    <div class="store-badges">
                        <a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Download di App Store"><img src="{{ asset('images/hsb-footer/app-store.svg') }}" alt="App Store" loading="lazy"></a>
                        <a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Download di Google Play"><img src="{{ asset('images/hsb-footer/google-play.svg') }}" alt="Google Play" loading="lazy"></a>
                    </div>
                    <img class="download-qr" src="{{ asset('images/hsb-footer/qr.webp') }}" alt="QR code download aplikasi HSB" width="110" height="110" loading="lazy">
                </div>
                <h3 class="footer-subheading">Hubungi Kami</h3>
                <a href="https://wa.me/628211019087" target="_blank" rel="noopener noreferrer">Whatsapp: +62 821-1019-087</a>
                <p>Hotline: +62 215-0122-288</p>
                <h3 class="footer-subheading">Follow Us</h3>
                <div class="footer-socials">
                    @foreach ($socials as [$name, $icon, $href])
                        <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $name }}"><img src="{{ asset("images/hsb-footer/$icon") }}" alt="" loading="lazy"></a>
                    @endforeach
                </div>
                <h3 class="footer-subheading">Join Komunitas HSB</h3>
                <div class="footer-socials"><a href="{{ $downloadUrl }}" target="_blank" rel="noopener noreferrer" aria-label="Komunitas HSB di Telegram"><img src="{{ asset('images/hsb-footer/telegram.svg') }}" alt="" loading="lazy"></a></div>
            </div>
        </div>

        <div class="footer-disclosures">
            <div class="footer-regulations">
                <h3>Legalitas</h3>
                <dl>
                    <div><dt>Badan Pengawas Perdagangan Berjangka Komoditi</dt><dd>001/BAPPEBTI/SI/05/2018<br>001/BAPPEBTI/SP-SPA/05/2018<br>03/BAPPEBTI/KEP-PBK/9/2018<br>003/BAPPEBTI/SP-PN/07/2020</dd></div>
                    <div><dt>Bank Indonesia</dt><dd>27/546/DPPK/Srt/B</dd></div>
                    <div><dt>Otoritas Jasa Keuangan</dt><dd>S-292/PM.02/2025</dd></div>
                    <div><dt>Bursa Komoditi dan Derivatif Indonesia</dt><dd>197/SPKB/ICDX/DIR/VI/2020</dd></div>
                    <div><dt>Indonesia Clearing House</dt><dd>026/OTC/ICH/DIR/VI/2020<br>178/SPKK/ICH/VI/2020</dd></div>
                    <div><dt>Keanggotaan Lembaga Asosiasi</dt><dd>1291/ASPEBTINDO/ANG-B/6/2018</dd></div>
                </dl>
            </div>
            <div class="footer-risk">
                <h3>Peringatan Risiko :</h3>
                <p>Produk dengan leverage memiliki tingkat risiko yang tinggi terhadap modal yang anda investasikan dan disarankan hanya menggunakan dana yang mampu anda tanggung apabila terjadi kerugian. Nilai investasi dapat turun atau naik dan Anda dapat kehilangan pembayaran margin awal anda. Harap diketahui bahwa produk dengan leverage belum tentu cocok untuk semua orang, jadi pastikan anda telah memahami sepenuhnya semua risiko yang terlibat.</p>
            </div>
        </div>

        <div class="footer-legal">
            <span>Copyright © {{ date('Y') }} HSB dilindungi undang-undang.</span>
            <div>
                <a href="{{ $base }}/fraud-warning" target="_blank" rel="noopener noreferrer">Waspada Penipuan</a>
                <a href="{{ $base }}/disclaimer" target="_blank" rel="noopener noreferrer">Kebijakan Privasi</a>
                <a href="{{ $base }}/karir" target="_blank" rel="noopener noreferrer">Karir</a>
                <a href="{{ $base }}/pengaduan-nasabah" target="_blank" rel="noopener noreferrer">Pengaduan Nasabah</a>
            </div>
        </div>
    </div>
</footer>
