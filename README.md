Introduction to the use of a helper.

## Redirect
redirects to another page
It works like this: redirect($path);

## Checking for duplicate login in the database
check for repeated login
It works like this: isLoginExists(PDO $pdo, string $login): bool;

# Messages
To use the messages correctly, you need to use either my css file or create your own for a more beautiful message appearance!

## Set Message
Here we set a message
It works like this: setMessage(string $key, string $message): void;

$key - message designation (used for hasMessage())
$message - your message

## has message
here we check in the php code to receive a message from another file, if there is, it will execute any code, otherwise nothing.
Example: <?php if (hasMessage('error')) : ?>
                        <div class="notice error">
                            <?php echo getMessage('error') ?>
                        </div>
                    <?php endif; ?>
                    
It works like this: hasMessage(string $key): bool;

## get message
here we get which key will be received and will display its message.
Example: <?php if (hasMessage('error')) : ?>
                        <div class="notice error">
                            <?php echo getMessage('error') ?>
                        </div>
                    <?php endif; ?>
                    
It works like this: getMessage(string $key) : string;

# PDO Insert
just insert sql for pdo
It works like this: pdoSet($allowed, &$values, $source = array());
example: $values = ["name from the database table" => value, ...];
          $sql = "INSERT INTO users SET ".pdoSet($allowed, $values, $values);
          $stm = $pdo->prepare($sql);
          $stm->execute($values);
          (just example)

# work with audio files
## findAudioFiles
searches for the path to the toe file in the database and returns an array of elements (example table: id, title, artist, category, relative_path);
It works like this: findAudioFiles();

## findNewSongs
It searches for the path to music that was recently added to the database, and returns it as an array (there is also a limit)
It works like this: findNewSongs($limit);

## findAudioForCategory
It searches for the path to music for category
It works like this: findAudioForCategory($category);

## findCategory
It searches category music in base date
It works like this: findCategory();
and return - return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

## delete Music
It delete music file and path (but folder saved :( )
It works like this: deleteMusic($id);
And then return message.

## removeExtension
if you need output name audio file in site, you can delete extension use this, example: .mp3, .wav and etc.
It works like this: removeExtension($fileName)

## searchSongs
if you need to create a search for music in your site, you can this command: searchSongs($searchQuery).
It works like this: searchSongs($searchQuery);

# Work With Video files

## findVideo
you can find all video from your date base use this command: findVideo();
example structure base date: id, user, title, author, photo, description, file_path, category.
it return array data of video files
it works like this: findVideo();

## find Category Video
it find video for category returned all category video from database
it works like this: find Category Video()

## findVideoForCategory
it works same as findAudioForCategory.
it works like this: findVideoForCategory($category).

## delete video
it works same as deleteAudio().
it works like this: deleteVideo($id);

## find new video
also work same as findNewAudio.
it works like this: findNewVideos($limit).

## get video id
it find id video from base date.
it works like this: getVideoById($id).

## search videos
also works same as search audio, but you need more values in your table, example: id, user, title, author, photo, description, file_path, category, upload_time.
it works like this: searchVideos($searchQuery).

# Comments (not under the video)
it get comments from base date (just comments about site example).
it works like this: getComments();

# I hope this "helper" help you, also you can send question or my mistake for my email: syneation@gmail.com


