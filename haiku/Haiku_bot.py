import tweepy
import time
from haiku_model import *


class TwitterAPI:
    """An object class for to create our Haiku bot using Twitter API"""
    def __init__(self):
        c_key = 'Y0Vs0vpELAZSXHAuETmsMuZRK'
        c_secret = '9iTWM7xnkfFRMlQpyLyRI8gR6gffyDaWd5i4rGLQbTGeRnvt7p'
        auth = tweepy.OAuthHandler(c_key, c_secret)
        access_token = '705015151771164672-LZupyvrDzYJWaQUxmND0oeHk9rNBGAg'
        access_token_secret = '4aeG5pIEzQrBAqPrEtNdIdNjotvG7DTbzFIi9mms1RPuz'
        auth.set_access_token(access_token, access_token_secret)
        self.api = tweepy.API(auth)

    def tweet(self, message):
        """A method to send Tweets"""
        self.api.update_status(status=message)


def tweet_haiku():
    """A function to tweet haikus"""
    haiku = HaikuModel()
    haiku_tweet = haiku.get_haiku()
    return haiku_tweet


def main():
    twitter = TwitterAPI()
    haiku_generator = HaikuModel()

    while True:
        haiku = haiku_generator.get_haiku()
        twitter.tweet(haiku)
        time.sleep(300) # tweet every five minutes while running.


if __name__ == "__main__":
    main()
