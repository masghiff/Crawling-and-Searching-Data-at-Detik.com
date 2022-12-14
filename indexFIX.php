<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Project Crawling and Searching Menggunakan Method KNN di Detik</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/sl-slide.css">
    <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>

<body>
    <!--Header-->
    <header class="navbar navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <a id="logo" class="pull-left" href="index.php"></a>
                <div class="nav-collapse collapse pull-right">
                    <ul class="nav">
                        <li class="active"><a href="index.php">Crawling Data</a></li>
                        <li><a href="search.php">Searching</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <!-- /header -->

    <!--Services-->
    <section id="services">
        <div class="container">
            <div class="center gap">
                <h3>Crawling - detik.com</h3>
                <form method="POST" action="">
                    <p class="lead">Input Keyword <input type="text" name="keyword"> <input type="submit" name="crawls" value="CRAWLS">
                    </p>
                </form>

                <?php
                    require_once __DIR__ . '/vendor/autoload.php';
                    include_once('simple_html_dom.php');

                    if (isset($_POST["crawls"])) 
                    {
                        

                        $key = str_replace(" ", "+",$_POST["keyword"]);
                        $html = file_get_html("https://www.detik.com/search/searchall?query=".$key."&siteid=2");
                        

                        $stemmerFact = new \Sastrawi\Stemmer\StemmerFactory();
                        $stemmer = $stemmerFact->createStemmer();

                        $stopwordFact = new \Sastrawi\StopWordRemover\StopWordRemoverFactory();
                        $stopword = $stopwordFact->createStopWordRemover();

                        echo "<b> Crawling Result<b>";
                        echo "<table border='1'>";
                        echo "<tr>";
                        echo "<th align='center'>Title</th>";
                        echo "<th align='center'>Link</th>";
                        echo "<th align='center'>Clean Data</th>";
                        echo "</tr>";
                        $con = new mysqli("localhost", "root", "", "news");
                        foreach ($html->find('article') as $news) 
                        {
                            $title = $news->find('h2[class="title"]', 0)->innertext;
                            $link = $news->find('a', 0)->href;

                            $output = $stemmer->stem($title);
                            $output = $stopword->remove($output);

                            echo "<tr>";
                            echo "<td>".$title."</td>";
                            echo "<td>".$link."</td>";
                            echo "<td>".$output."</td>";
                            echo "</tr>";

                            $sql = 'INSERT INTO contents (title, link, clean_data) VALUES ("'.$title.'", "'.$link.'", "'.$output.'")';
                            mysqli_query($con, $sql);
                        }
                        echo "</table>";
                        mysqli_close($con);
                    }
                ?>
            </div>
        </div>
    </section>
    <!--/Services-->

    <!--Footer-->
    <footer id="footer">
        <div class="container">
            <div class="row-fluid">
                <div class="span5 cp">
                    &copy; 2016 <a target="_blank" href="http://www.domain.com/">Your Company</a>. All Rights Reserved.
                </div>
                <div class="span6">
                    <ul class="social pull-right">
                        <li><a href="#"><i class="icon-facebook"></i></a></li>
                        <li><a href="#"><i class="icon-twitter"></i></a></li>
                        <li><a href="#"><i class="icon-pinterest"></i></a></li>
                        <li><a href="#"><i class="icon-linkedin"></i></a></li>
                        <li><a href="#"><i class="icon-google-plus"></i></a></li>
                        <li><a href="#"><i class="icon-youtube"></i></a></li>
                        <li><a href="#"><i class="icon-instagram"></i></a></li>
                    </ul>
                </div>
                <div class="span1">
                    <a id="gototop" class="gototop pull-right" href="#"><i class="icon-angle-up"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <!--/Footer-->
</body>

</html>