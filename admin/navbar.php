<nav class="navbar">
    <a href="main.php">
        <div class="navl vex">
            <h1>Swiftly&nbsp;<h3>Admin</h3>
            </h1>
        </div>
    </a>
    <div class="navr vex">
        <button class="btnf" onclick="toggleTheme()">
            <svg id="light" width="16" height="16">
                <use xlink:href="../img/all.svg#themeToggleLight"></use>
            </svg>
            <svg id="dark" width="16" height="16">
                <use xlink:href="../img/all.svg#themeToggleDark"></use>
            </svg>
        </button>
        <ul>
            <a href="main.php">
                <li class="vex">
                    <svg width="16" height="16">
                        <use xlink:href="../img/all.svg#home"></use>
                    </svg>
                    Home
                </li>
            </a>
            <div class="navdrop">
                <li class="vex">Yönetim<svg width="16" height="16">
                        <use xlink:href="../img/all.svg#dropDown"></use>
                    </svg></li>
                <div class="navdrop-c">
                    <a href="product.php">
                        <li>Ürün</li>
                    </a>
                    <br>
                    <a href="staff.php">
                        <li>Personel</li>
                    </a>
                    <br>
                    <a href="manager.php">
                        <li>Yönetici</li>
                    </a>
                    <br>
                    <a href="sale.php">
                        <li>Satış Kayıtları</li>
                    </a>
                    <br>
                    <a href="statics.php">
                        <li>İstatistikler</li>
                    </a>
                    <br>
                    <a href="registry.php">
                        <li>Kayıt Defteri</li>
                    </a>
                </div>
            </div>
            <button class="btn-f btr vex" id="logoutButton" data-confirm="Çıkış yapmak istediğinize emin misiniz?"
                data-redirect="../index.php?admin=true">
                <svg width="18" height="18">
                    <use xlink:href="../img/all.svg#logout"></use>
                </svg>
            </button>
        </ul>
    </div>
</nav>
<br>
<br>
<br>