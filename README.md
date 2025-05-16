# Introduction to the use of a helper.

## Redirect
redirects to another page
[!NOTE]
It works like this: header("Location: $path");

## Checking for duplicate login in the database
check for repeated login
[!NOTE]
It works like this: isLoginExists(PDO $pdo, string $login): bool;

# Messages
[!WARNING]
To use the messages correctly, you need to use either my css file or create your own for a more beautiful message appearance!

## Set Message
Here we set a message
[!NOTE]
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
[!NOTE]
It works like this: hasMessage(string $key): bool;

## get message
here we get which key will be received and will display its message.
Example: <?php if (hasMessage('error')) : ?>
                        <div class="notice error">
                            <?php echo getMessage('error') ?>
                        </div>
                    <?php endif; ?>
[!NOTE]
It works like this: getMessage(string $key) : string;

# PDO Insert
just insert sql for pdo
[!NOTE]
It works like this: pdoSet($allowed, &$values, $source = array());

# While is not full documantation


