<?php
/*
Copyright 2015-2016 Daniil Gentili
(https://daniil.it)
This file is part of the dl2cloud (https://github.com/danog/dl2cloud).
Dl2cloud is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
Dl2cloud is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU Affero General Public License for more details.
You should have received a copy of the GNU General Public License along with the dl2cloud.
If not, see <http://www.gnu.org/licenses/>.
*/
function declareverify($email)
{
    global $subject;
    global $body;
    global $htmlbody;
    global $username;

    $subject = ',^^ Email verification ^^,';

    $htmlbody = "Hello $username!
<BR><BR>
You, or someone else, signed up to </a href=\"http://2dropbox.daniil.it\" TARGET=\"_blank\">2dropbox</a>
<BR><BR>
To start downloading files to Dropbox please verify your email address by pressing the following button: <BR>
<a HREF=\"http://api.daniil.it/dl/?verify=y&amp;hash=$hash&amp;email=$email\"><button VALUE=\"Click me!\"></button></a><BR><br>If you can&apos;t click the button please copy and paste the following url in your browser: <BR><BR>
===============<BR>
http://api.daniil.it/dl/?action=verify&amp;hash=$hash&amp;account=$email
===============<BR>
<BR>
If you didn't sign up to this service simply ignore this email.
<BR>
I hope you have a good time downloading files 2dropbox!<BR>
Bye!<BR>
<a href=\"http://daniil.it\">Daniil Gentili</a>
";


    $body = "Hello $username!

You, or someone else, signed up to 2dropbox (http://2dropbox.daniil.it).

To start downloading files to Dropbox please verify your email address by copying and pasting the following url in your browser: 
===============
http://api.daniil.it/dl/?action=verify&hash=$hash&account=$email
===============

If you didn't sign up to this service simply ignore this email.

I hope you have a good time downloading files 2dropbox!
Bye!
Daniil Gentili (http://daniil.it)
";
}


function declarewelcome()
{
    global $subject;
    global $body;
    global $htmlbody;
    global $username;

    $subject = ',^^ Welcome! ^^,';
    $htmlbody = "Welcome to 2dropbox, $username!
<BR><BR>
You have successfully verified your email!<BR>
I hope you have a good time downloading files 2dropbox!<BR>
Bye!<BR>
<a href=\"http://daniil.it\">Daniil Gentili</a>
";

    $body = "Welcome to 2dropbox, $username!

You have successfully verified your email!

I hope you have a good time downloading files 2dropbox!
Bye!
Daniil Gentili (http://daniil.it)
";
}
