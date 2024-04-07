# Playlist Creator

App to add recently broadcast songs from radio stations to your YouTube playlists.

## Prerequisites

To set up the necessary credentials for using the *YouTube Data API v3*, follow these steps:
- Sign in to your Google account at google.com and navigate to https://console.cloud.google.com/apis/credentials.

- Create a new YouTube API key with access to the *YouTube Data API v3*. Rename `bin/api-key.dist.php` to `bin/api-key.php` and insert your API key there.

- Create a new OAuth 2.0 client ID and client secret. Copy the client ID and client secret to the `bin/client-secret.json` file.

In case the default system CA bundle is not found, when running the app, you need to set the `curl.cainfo` option in your php.ini:
- Download the CA bundle from https://curl.se/ca/cacert.pem.
- Set `curl.cainfo` to the bundle's location in your php.ini: `curl.cainfo = '<your_path>\cacert.pem'`


## Installation

Clone the repository and install dependencies:
 
```
composer install
```

## Usage

### Create a playlist from broadcast songs:

```
  USAGE:
    php bin/playlist.php create [OPTIONS] <station_name>

  ARGUMENTS:
    <station_name>               The name of the radio station to extract songs from.

  OPTIONS:      
       --id <playlist_id>        The id of an existing YouTube playlist to add songs to. 
                                 If not provided a new playlist is created.
       --title <playlist_title>  The title of the YouTube playlist to create. 
                                 If not provided, a default title will be used.
    -d --date <date>             The starting date of the broadcast 
                                 (format Y-m-d, e.g. 2022-03-09). [default: today]
    -t --time <time>             The starting time of the broadcast 
                                 (format H:i, e.g. 03:00). [default: 00:00]
    -u --duration <duration>     The duration of the broadcast (format G:i, e.g. 1:30). 
                                 Either duration or limit must be set.
    -c --limit <limit>           The number of songs to add to the playlist. 
                                 Either duration or limit must be set.
    -i --interactive             Ask for confirmation before adding each song to the playlist.
```

#### Examples:

To create a new playlist with the title "my_wdr4" that contains the titles of the songs broadcast on WDR 4 on March 12, 2023, between 9:00 AM and 10:00 AM:
```
php bin/playlist.php create --date=2023-03-12 --time=09:00 --duration=1:00 --title=my_wdr4 wdr4
```

To add the titles of the first 50 songs broadcast on WDR 4 on March 12, 2023, after 8:00 PM to an existing playlist with the ID "PL1234567890":
```
php bin/playlist.php create --date=2023-03-12 --time=20:00 --limit=50 --id=PL1234567890 wdr4
```

### List broadcast songs:  

```
  USAGE:
    php bin/playlist.php show [OPTIONS] <station_name>

  ARGUMENTS:
    <station_name>               The name of the radio station to list songs from.

  OPTIONS:      
    -d --date <date>             The starting date of the broadcast 
                                 (format Y-m-d, e.g. 2022-03-09). [default: today]
    -t --time <time>             The starting time of the broadcast 
                                 (format H:i, e.g. 03:00). [default: 00:00]
    -u --duration <duration>     The duration of the broadcast (format G:i, e.g. 1:30). 
                                 Either duration or limit must be set.
    -c --limit <limit>           The number of songs to list. 
                                 Either duration or limit must be set.
```


#### Examples:


To list the titles of the 20 songs broadcast on WDR 4 on March 12, 2023, starting from 9:00 PM:
```
php bin/playlist.php show --date=2023-03-12 --time=21:00 --limit=20 wdr4
```

Note that only songs that have been broadcast within the last seven days are supported.
Additionally, only songs that are available on YouTube will be added to the playlist.


### List supported radio stations

```
  USAGE:
    php bin/playlist.php stations
```
    
You can add radio stations by defining them in the `bin/stations.json` file:

```
"<station_id>": {
    "name": "<title>",
    "path": "<path>"
}
```
where

|                |                                                                                                                          |
|----------------|--------------------------------------------------------------------------------------------------------------------------|
| `<station_id>` | the value for the <station_name> argument (e.g. wdr4)                                                                    |
| `<title>`      | the title for newly created playlists, if none is given (e.g. WDR 4)                                                     |
| `<path>`       | a part of the url to the playlist on onlineradiobox.com<br> (e.g. wdr4 for https://onlineradiobox.com/de/wdr4/playlist/) |

