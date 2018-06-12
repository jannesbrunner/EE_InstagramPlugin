# EE_InstagramPlugin
A little Instagram Feed plugin written in PHP for [ExpressionEngine CMS](https://expressionengine.com/)

You can display several things like last uploaded pictures and more on your ExpressionEngine front end.

_Note: This is also me getting my feet wet PHP, feel free to use/improve!_ 

## Setup

1. Extract the ZIP archive and add the instagram_feed folder to 
system/user/addons 

2. Install the plugin via EE Backend.

(Additional for Access Token)

3. go to https://www.instagram.com/developer/ and click on _Manage Clients_

4. Add a new Client, fill out the Form. You will get an AccessToken at the end.


## Usage

__You will need to always include your Instagram Access token on every single or tag pair in use. All provided data is in relation to the user associated with the token.__

Example (Single Tag) <br />
`{exp:instagram_feed:self_username at="YOUR TOKEN GOES HERE"}` <br />
Example (Pair Tag) <br />
```
{exp:instagram_feed:get_user at="YOUR TOKEN GOES HERE"}
[..]
{/exp:instagram_feed:get_user}
```

### Available Single Tags

`{exp:instagram_feed:self_picture}` <br />
Returns the profile picture set on Instagram <br />

Example Return: `http://distillery.s3.amazonaws.com/profiles/profile_1574083_75sq_1295469061.jpg`

`{exp:instagram_feed:self_username}` <br />
Returns the users username set on Instagram <br />
Example Return: `snoopdogg`

`{exp:instagram_feed:self_fullname}` <br />
Returns the users username set on Instagram <br />
Example Return: `Snoop Dogg`

`{exp:instagram_feed:self_bio}` <br />
Returns the users bio set on Instagram <br />
Example Return: `This is my bio!`

`{exp:instagram_feed:self_website}` <br />
Returns the users website link set on Instagram <br />
Example: Return: `http://snoopdogg.com`

`{exp:instagram_feed:self_profile}` <br />
Returns the users website link set on Instagram <br />
Example Return: `http://snoopdogg.com`

`{exp:instagram_feed:self_totalposts}` <br />
Returns the users total amount of posts on Instagram <br />
Example Return: `1320`

`{exp:instagram_feed:self_totalfollower}` <br />
Returns the usesr total amount of follower on Instagram <br />
Example Return: `1530`
 
`{exp:instagram_feed:self_totalfollows}` <br />
Returns the users total amout of follows on Instagram <br />
Example Return: `430`

### Pair Tags
You can use the above mentioned single tags combined within this tag pair construct. 
```{exp:instagram_feed:get_user}__
{picture} {username} {fullname} {bio} {website_link} {profile_link}
{total_posts} {total_follower} {total_follows}
{/exp:instagram_feed:get_user}
```

Gets the 20 recent Instagram posts, defaulted to 20.
Set limit parameter for different amount. (e.g. limit="5")

```{exp:instagram_feed:get_recent}
{picture} The Picture as link
{comments} Total amount of comments on the post
{likes} Total amount of likes on the post
{link} Link to the post
{/exp:instagram_feed:get_recent}
```

Searches for tags by the given attribute.
Returns a list found tagnames and their amount of posts on Instagram
Leave out the leading '#' Set limit parameter for different amount. (e.g. limit="5")
```
{exp:instagram_feed:tag_search hashtag="HASHTAG"}
{name} The name of the fount hashtag
{media_count} The amount of posts related to the hashtag
{/exp:instagram_feed:tag_search}
```
