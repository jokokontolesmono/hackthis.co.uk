<?php
    $custom_css = array('profile.scss', 'confirm.css');
    $custom_js = array('jquery.confirm.js', 'profile.js');
    require_once('init.php');
    
    $profile = new profile($_GET['user']);
    if (isset($profile->uid))
        $app->page->title = $profile->username;

    require_once('header.php');
    

    if (!isset($profile->uid)):
        $app->utils->message("User not found");
        require_once('footer.php');
        die();
    endif;

    // FRIENDS LIST
    if (isset($_GET['friends']) && count($profile->friendsList)):
?>
        <div id="friends-search" class="mobile-hide">
            <input placeholder="Search friends">
            <i class="icon-search"></i>
        </div>
        <a href='/user/<?=$profile->username;?>'><i class='icon-caret-left'></i> <?=$profile->username;?>'s profile</a>
        <br/><br/>
        <ul class='users-list'>
<?php
        foreach($profile->friendsList as $friend):
            if (isset($friend->image)) {
                $gravatar = isset($friend->gravatar) && $friend->gravatar == 1;
                $friend->image = profile::getImg($friend->image, 48, $gravatar);
            } else
                $friend->image = profile::getImg(null, 48);
?>
            <li>
                <a href='/user/<?=$friend->username;?>'>
                    <img src='<?=$friend->image;?>' width='100%' alt='<?=$friend->username;?> profile picture'/>
                    <div>
                        <span><?=$friend->username;?></span><br/>
                        Score: <?=$friend->score;?><br/>
                        <?=($friend->status)?'Friends':'';?>
                    </div>
                </a>
            </li>
<?php   endforeach; ?>
        </ul>

<?php
        require_once('footer.php');
        die();
    endif; // End friends list

    /* USERS PROFILE STARTS */
?>
    <article class='profile'>
<?php if ($profile->friends):?>
        <a href='#' class='button button-blank right removefriend' data-uid='<?=$profile->uid;?>'><i class='icon-user'></i> Friends</a>
<?php elseif ($profile->friends !== NULL && $profile->friend != $profile->uid): ?>
        <a href='#' class='button right button-disabled'>Pending</a>
<?php elseif ($profile->friends !== NULL && $profile->friend == $profile->uid): ?>
        <a href='#' class='button right acceptfriend' data-uid='<?=$profile->uid;?>'><i class='icon-addfriend'></i> Accept</a>
<?php else: ?>
        <a href='#' class='button right addfriend' data-uid='<?=$profile->uid;?>'><i class='icon-addfriend'></i> Add friend</a>
<?php
    endif;

    if ($profile->owner):
?>
        <a href='/settings/' class='button right'><i class='icon-edit'></i> Edit profile</a>
<?php else: ?>
        <a href='/inbox/compose?to=<?=$profile->username;?>' class='messages-new button right' data-to="<?=$profile->username;?>"><i class='icon-envelope-alt'></i> PM user</a>
<?php endif; ?>

        <h1 class='lower'><?=$profile->username;?></h1>
<?php if ($profile->admin): ?>
        <strong>Administrator</strong>
<?php elseif ($profile->moderator): ?>
        <strong>Moderator</strong>
<?php endif; ?>

        <section class='fluid clr'>
            <div class='col span_7 clr'>
                <img src='<?=$profile->image;?>' width='100%' alt='<?=$profile->username;?> profile picture'/><br/>
                <div class='progress-container'><div class='progress' style='width: 90%'>90%</div></div>            
            </div>

            <div class='col span_17 clr'>
                <div class='profile-feed scroll'>
                    <ul class='content'>
<?php
    foreach($profile->feed as $item):
?>
                        <li>
                            <i class='icon-<?=$item['icon'];?>'></i> <?=$item['string'];?>
                            <span class='dark'>· <time datetime="<?=date('c', strtotime($item['time']));?>"><?=$app->utils->timeSince($item['time']);?></time></span>
<?php
        if ($profile->owner):
?>
                            <a class='right hide remove' data-fid='<?=$item['id'];?>' href='#'><i class='icon-remove'></i></a>
<?php
        endif;
?>
                        </li>
<?php
    endforeach;
?>
                    </ul>
                </div>
            </div>
        </section>

        <section class='profile-extra row fluid'>
            <div class='col span_7 clr'>
<?php
    /* MEDALS */

    $medalCount = count($profile->medals);
    if ($medalCount):
?>
                <section class='row'>
                    <ul class='medals clr'>
<?php
        foreach ($profile->medals as $medal):
?>
                        <li class="medal medal-<?=$medal->colour;?>"><a href='/medals.php#<?=strtolower($medal->label);?>'><?=$medal->label;?></a></li>
<?php
        endforeach;
?>
                    </ul>
                </section>
<?php
    endif;


    /* FRIENDS */

    $friendCount = count($profile->friendsList);
    if ($friendCount):
?>
                <section class='row'>
                    <h2><a href='/user/<?=$profile->username;?>/friends'><?=$friendCount;?> Friend<?=($friendCount==1?'':'s');?></a></h2>

                    <ul class='friends-list'>
<?php     
        $i = 0;
        foreach($profile->friendsList as $friend):
            $i++;
            if ($i > 8)
                break;

            if (isset($friend->image)) {
                $gravatar = isset($friend->gravatar) && $friend->gravatar == 1;
                $friend->image = profile::getImg($friend->image, 48, $gravatar);
            } else
                $friend->image = profile::getImg(null, 48);
?>
                        <li>
                            <figure>
                                <a href='/user/<?=$friend->username;?>'>
                                    <img src='<?=$friend->image;?>' width='100%' alt='<?=$friend->username;?> profile picture'/>
                                </a>
                                <figcaption>
                                    <a href='/user/<?=$friend->username;?>'><?=$friend->username;?></a><br/>
                                    Score: <?=$friend->score;?><br/>
                                    <?=($friend->status)?'Friends':'';?>
                                </figcaption>
                            </figure>
                        </li>
<?php   endforeach; ?>
                    </ul>
                </section>
<?php
    endif;

    if ($profile->lastfm):
?>
                <section class='row music'>
                    <a class='right hide-external icon-hover' href='http://www.last.fm/'><i class='icon-lastfm'></i></a>
                    <h2><a href='http://www.last.fm/user/<?=$profile->lastfm;?>'>Music</a></h2>
                    <div data-user="<?=$profile->lastfm;?>" class="profile-music loading">
                        <img src='/files/images/icons/loading.gif' class='icon'/>
                    </div>
                </section>
<?php
    endif;
?>
                &nbsp;
            </div>

            <div class='col span_17 clr'>
                <section class='profile-details row fluid'>
                    <ul class='clr'>
<?php
    $profile->printItem("Score", $profile->score);
    $profile->printItem("<a href='#'>Posts</a>", 1025);
    $profile->printItem("<a href='#'>Articles</a>", 2);
    $profile->printItem("<a href='faq.php#karma'>Karma</a>", 0);
?>
                    </ul>
                    <ul class='clr'>
<?php
    if ($profile->show_name) { $profile->printItem("Name", $profile->name); }
    if ($profile->show_email) { $profile->printItem("Email", $profile->email); }
    if ($profile->show_gender) { $profile->printItem("Gender", $profile->gender, false, true); }
    $profile->printItem("DOB", $profile->getDob());
    $profile->printItem("Joined", $profile->joined, true);
    $profile->printItem("Last seen", $profile->last_active, true);
?>
                    </ul>
<?php                    
    if (count($profile->social)):
?>               
                    <ul class='social clr'>

<?php
        foreach($profile->social as $social):
?>
                        <li><a class='hide-external' href='<?=$social['uri'];?>'><i class='icon-<?=$social['icon'];?>'></i></a></li>
<?php
        endforeach;
?>
                    </ul>
<?php
    endif;
?>
                </section>
<?php
    if (isset($profile->about))
        echo $profile->about;
?>
            </div>
        </section>
    </article>
<?php
    require_once('footer.php');
?>