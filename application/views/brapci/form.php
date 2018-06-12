<form>
	<?php echo msg('search_term'); ?>
	<textarea class="form-control" name="q" id="q"></textarea>
	<input type="radio" name="type" value="1">
	<?php echo msg('search_1'); ?>
	<input type="radio" name="type" value="2">
	<?php echo msg('search_2'); ?>
	<input type="radio" name="type" value="3">
	<?php echo msg('search_3'); ?>
	<input type="radio" name="type" value="4">
	<?php echo msg('search_4'); ?>
	<input type="radio" name="type" value="5">
	<?php echo msg('search_5'); ?>
	<input type="radio" name="type" value="6">
	<?php echo msg('search_6'); ?>
</form>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<form class="example" action="action_page.php">
	<?php echo msg('search_term'); ?>
	<br>
	<input type="text" placeholder="Search.." name="search" style="width: 90%;">
	<button type="submit">
		<i class="fa fa-search"></i>
	</button>
	<br>
	<input type="radio" name="type" value="1">
	<?php echo msg('search_1'); ?>
	<input type="radio" name="type" value="2">
	<?php echo msg('search_2'); ?>
	<input type="radio" name="type" value="3">
	<?php echo msg('search_3'); ?>
	<input type="radio" name="type" value="4">
	<?php echo msg('search_4'); ?>
	<input type="radio" name="type" value="5">
	<?php echo msg('search_5'); ?>
	<input type="radio" name="type" value="6">
	<?php echo msg('search_6'); ?>
</form>

<style>
    .cf:before, .cf:after {
        content: "";
        display: table;
    }

    .cf:after {
        clear: both;
    }

    .cf {
        zoom: 1;
    }

    /* Form wrapper styling */
    .search-wrapper {
        width: 220px;
        margin: 45px auto 50px auto;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .4) inset, 0 1px 0 rgba(255, 255, 255, .2);
    }

    /* Form text input */

    .search-wrapper input {
        width: 138px;
        height: 20px;
        padding: 10px 5px;
        float: left;
        font: bold 15px 'lucida sans', 'trebuchet MS', 'Tahoma';
        border: 0;
        background: #EEE;
        border-radius: 3px 0 0 3px;
    }

    .search-wrapper input:focus {
        outline: 0;
        background: #fff;
        box-shadow: 0 0 2px rgba(0,0,0,.8) inset;
    }

    .search-wrapper input::-webkit-input-placeholder {
        color: #999;
        font-weight: normal;
        font-style: italic;
    }

    .search-wrapper input:-moz-placeholder {
        color: #999;
        font-weight: normal;
        font-style: italic;
    }

    .search-wrapper input:-ms-input-placeholder {
        color: #999;
        font-weight: normal;
        font-style: italic;
    }

    /* Form submit button */
    .search-wrapper button {
        overflow: visible;
        position: relative;
        float: right;
        border: 0;
        padding: 0;
        cursor: pointer;
        height: 40px;
        width: 72px;
        font: bold 15px/40px 'lucida sans', 'trebuchet MS', 'Tahoma';
        color: white;
        text-transform: uppercase;
        background: #D83C3C;
        border-radius: 0 3px 3px 0;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, .3);
    }

    .search-wrapper button:hover {
        background: #e54040;
    }

    .search-wrapper button:active, .search-wrapper button:focus {
        background: #c42f2f;
        outline: 0;
    }

    .search-wrapper button:before {/* left arrow */
        content: '';
        position: absolute;
        border-width: 8px 8px 8px 0;
        border-style: solid solid solid none;
        border-color: transparent #d83c3c transparent;
        top: 12px;
        left: -6px;
    }

    .search-wrapper button:hover:before {
        border-right-color: #e54040;
    }

    .search-wrapper button:focus:before, .search-wrapper button:active:before {
        border-right-color: #c42f2f;
    }

    .search-wrapper button::-moz-focus-inner {/* remove extra button spacing for Mozilla Firefox */
        border: 0;
        padding: 0;
    }
</style>

<!DOCTYPE html>
<html lang="en" >

	<head>
		<meta charset="UTF-8">
		<title>Search Box</title>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.min.js" type="text/javascript"></script>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">

		<style>
            /* NOTE: The styles were added inline because Prefixfree needs access to your styles and they must be inlined if they are on local disk! */
            .cf:before, .cf:after {
                content: "";
                display: table;
            }

            .cf:after {
                clear: both;
            }

            .cf {
                zoom: 1;
            }

            /* Form wrapper styling */
            .search-wrapper {
                width: 220px;
                margin: 45px auto 50px auto;
                box-shadow: 0 1px 1px rgba(0, 0, 0, .4) inset, 0 1px 0 rgba(255, 255, 255, .2);
            }

            /* Form text input */

            .search-wrapper input {
                width: 90%;
                height: 20px;
                padding: 10px 5px;
                float: left;
                font: bold 15px 'lucida sans', 'trebuchet MS', 'Tahoma';
                border: 0;
                background: #EEE;
                border-radius: 3px 0 0 3px;
            }

            .search-wrapper input:focus {
                outline: 0;
                background: #fff;
                box-shadow: 0 0 2px rgba(0,0,0,.8) inset;
            }

            .search-wrapper input::-webkit-input-placeholder {
                color: #999;
                font-weight: normal;
                font-style: italic;
            }

            .search-wrapper input:-moz-placeholder {
                color: #999;
                font-weight: normal;
                font-style: italic;
            }

            .search-wrapper input:-ms-input-placeholder {
                color: #999;
                font-weight: normal;
                font-style: italic;
            }

            /* Form submit button */
            .search-wrapper button {
                overflow: visible;
                position: relative;
                float: right;
                border: 0;
                padding: 0;
                cursor: pointer;
                height: 40px;
                width: 72px;
                font: bold 15px/40px 'lucida sans', 'trebuchet MS', 'Tahoma';
                color: white;
                text-transform: uppercase;
                background: #D83C3C;
                border-radius: 0 3px 3px 0;
                text-shadow: 0 -1px 0 rgba(0, 0, 0, .3);
            }

            .search-wrapper button:hover {
                background: #4040e5;
            }

            .search-wrapper button:active, .search-wrapper button:focus {
                background: #2f2fc4;
                outline: 0;
            }

            .search-wrapper button:before {/* left arrow */
                content: '';
                position: absolute;
                border-width: 8px 8px 8px 0;
                border-style: solid solid solid none;
                border-color: transparent #3c3cd8 transparent;
                top: 12px;
                left: -6px;
            }

            .search-wrapper button:hover:before {
                border-right-color: #4040e5;
            }

            .search-wrapper button:focus:before, .search-wrapper button:active:before {
                border-right-color: #2f2fc4;
            }

            .search-wrapper button::-moz-focus-inner {/* remove extra button spacing for Mozilla Firefox */
                border: 0;
                padding: 0;
            }

		</style>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/prefixfree/1.0.7/prefixfree.min.js"></script>

		<form action="/search.html" class="search-wrapper cf form-control">
			<input type="text" placeholder="Search here..." required="" >
			<button type="submit">
				Search
			</button>
		</form>
