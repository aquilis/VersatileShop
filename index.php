<?php
include 'lib/utils.php';
?>
<html>
    <head>
        <title>Versatile shop</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <link href="css/bootstrap.css" rel="stylesheet">
                    <link href="css/style.css" rel="stylesheet">
                    <script src="js/jquery-1.11.0.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/jquery.i18n.properties.js"></script>
        <script src="js/utils.js"></script>
        <script src="js/language-utils.js"></script>
        <script>
            $(document).ready(function () {
                $(".nav li[id=header-home]").addClass("active");
                languageUtils.applyLabelsToHTML(utils.initializeHeaderBehaviour);
            });
        </script>
    </head>

    <body class="paper-textured">
        <?php include_once("templates/header.php"); ?>
        <div id="mainColumn">
            <div class="carousel-holder">
                <h3> <span i18n_label="home.page.caption"></span> <h3>
                <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                        <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                    </ol>

                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="images/gta5.jpg" alt="First slide">
                            <div class="carousel-caption">
                                <h3>GTA 5 for Xbox and Playstation</h3>
                                <p>Don't miss the new hit! Order now!</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="images/outlast.jpg" alt="Second slide">
                            <div class="carousel-caption">
                                <h3>Outlast</h3>
                                <p>Follow the blood. Emrace the darkness</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="images/deus-ex.jpg" alt="Third slide">
                            <div class="carousel-caption">
                                <h3>Deus ex: the fall</h3>
                                <p></p>
                            </div>
                        </div>
                    </div>

                    <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
            </div>

            <div class="section">
                <div class="page-header">
                    <h1><span i18n_label="home.page.whats.hot.large"></span>
                        <small><span i18n_label="home.page.whats.hot.small"></span></small></h1>
                </div>
                <div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object" src="images/gta5-box.jpg" alt="Some pic">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">GTA V discounts, only this months</h4>
                        Los Santos: a sprawling sun-soaked metropolis full of self-help gurus, starlets and fading celebrities, once the envy of the Western world,
                        now struggling to stay afloat in an era of economic uncertainty and cheap reality TV. Amidst the turmoil, three very different criminals 
                        plot their own chances of survival and success: Franklin, a street hustler looking for real opportunities and serious money; Michael, a professional
                        ex-con whose retirement is a lot less rosy than he hoped it would be; and Trevor, a violent maniac driven by the chance of a cheap high and the next
                        big score. Running out of options, the crew risks everything in a series of daring and dangerous heists that could set them up for life.
                    </div>
                </div>
                <div class="media">
                    <a class="pull-right" href="#">
                        <img class="media-object" src="images/outlast-cover.jpg" alt="Some pic">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">Outlast released!</h4>
                        Miles Upshur, a freelance journalist, receives a tip-off from an anonymous source, known only as "The Whistleblower", about Mount Massive Asylum,
                        a psychiatric hospital owned and operated by the Murkoff corporation. Upon gaining entry to the asylum, he finds the bodies of the asylum's staff 
                        strewn about the hallways, and the now escaped inmates, known as "The Variants", roaming the grounds. Progressing through the upper dormitories, 
                        he encounters an impaled SWAT officer, who in his dying moments tells the journalist to get out of the asylum while he still can. Exiting the dormitories, 
                        Upshur is attacked by a powerful inmate named Chris Walker, who throws him through a window, and down to the atrium. Upon regaining consciousness, he 
                        encounters "Father" Martin, an inmate who believes he is a priest. Martin says Upshur was sent by God to be a witness to his cult and has to stay in the
                        asylum, and then departs as Upshur passes out again.
                    </div>
                </div>
                <div class="media">
                    <a class="pull-left" href="#">
                        <img class="media-object" src="images/deus-ex-cover.jpg" alt="Some pic">
                    </a>
                    <div class="media-body">
                        <h4 class="media-heading">Deus ex: The fall</h4>
                        Los Santos: a sprawling sun-soaked metropolis full of self-help gurus, starlets and fading celebrities, once the envy of the Western world,
                        In their safe house in Costa Rica, Ben Saxon and Anna Kelso are hiding from the Illuminati, their henchman Jaron Namir, and his covert black
                        ops unit, the Tyrants. Anna mourns the death of her superior, Matt Ryan, who was killed by the Tyrants. Ben recalls the loss of his squadmate,
                        Sam Duarte, in the Australian civil war, and how Duarte's death lead to Ben's recruitment, and ultimately his falling out, with the Tyrants.
                        =					  Both Anna and Ben are beginning to suffer the effects of mechanical augmentation rejection because of a global shortage of Neuropozyne, an 
                        anti-rejection drug that augmented humans must take to avoid augmentation rejection. Following advice from the mysterious Janus, Saxon travels to Panama City to acquire more. 
                    </div>
                </div>

            </div>

        </div>
        <?php include_once("templates/footer.php"); ?>

    </body>

</html>