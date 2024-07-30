<nav class="navbar">
        <div class="navl vex">
            <h1>Swiftly</h1>
        </div>
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
                    <li>Ana sayfa</li>
                </a>
                <a href="sale.php">
                    <li>Satış</li>
                </a>
                <button class="btn-f btr vex" id="logoutButton" data-confirm="Çıkış yapmak istediğinize emin misiniz?"
                data-redirect="../index.php?staff=true">
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