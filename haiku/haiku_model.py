import gzip
import glob
import pickle
import random
import tokenization
from collections import *


class HaikuModel:
    """To run this class follow these steps: 1. Type 'python3' in the linux
    shell. (be sure that you are in the repository) 2. Type 'import
    haiku_model' 3. Type 'haiku = haiku_model.haiku_model()' (it will now
    create the .pickle files, this could take a while.) 4. Use a get or
    search method to return a haiku.
    """

    def __init__(self):
        """Assigns instance variables. Some variable values are stored in
        .pickle files and they will be retrieved and assigned. If a .pickle
        file doesn't exist it will be created.
        """

        directory_content = glob.glob('*.pickle')
        if not 'tweets.pickle' in directory_content:
            self.generate_tweet_list()
        with open('tweets.pickle', 'rb') as f:
            self.tweet_list = pickle.load(f)
        if not 'syllables.pickle' in directory_content:
            self.generate_syllables_dict()
        with open('syllables.pickle', 'rb') as f:
            self.syllables_dict = pickle.load(f)
        if not 'haiku.pickle' in directory_content:
            self.generate_haiku_list()
        with open('haiku.pickle', 'rb') as f:
            self.haiku_list = pickle.load(f)
        if not 'haiku_by_word.pickle' in directory_content:
            self.generate_haiku_by_word()
        with open('haiku_by_word.pickle', 'rb') as f:
            self.haiku_by_word = pickle.load(f)
        self.generate_hashtag_haiku_list()
        self.get_pop_hashtags()

    def generate_tweet_list(self):
        """Dumps a list of tuples (containing usernames and tweets) in a
        .pickle file.
        """

        list_of_files = glob.glob('201012/20101231:*.out.gz')
        tweet_list = []
        for file_name in list_of_files:
            with gzip.open(file_name, 'rt') as f:
                file_content = ' '
                while file_content != '':
                    file_content = f.readline()
                    split_line = file_content.split(' ')
                    username = split_line.pop(0)
                    tweet = ' '.join(split_line)
                    tweet = tweet[:-1]
                    tweet_list.append((username, tweet))
                f.close()
        with open('tweets.pickle', 'wb') as f:
            pickle.dump(tweet_list, f)

    def generate_syllables_dict(self):
        """Dumps a dictionary with words and their number of syllables in
        a .pickle file.
        """

        syllables_dict = defaultdict(int)
        with open('dpw.cd', 'r') as f:
            file_content = ' '
            while file_content != '':
                file_content = f.readline()
                split_line = file_content.split('\\')
                if len(split_line) == 6:
                    word = split_line[1]
                    syllable_count = 0
                    for char in split_line[5]:
                        if char == '[':
                            syllable_count += 1
                syllables_dict[word] = syllable_count
        with open('syllables.pickle', 'wb') as f:
            pickle.dump(syllables_dict, f)

    def generate_haiku_list(self):
        """Dumps a list of haiku's in a .pickle file."""

        haiku_list = []
        for (username, tweet) in self.tweet_list:
            syllables_counter = 0
            stage_counter = 0
            split_tweet = tweet.split()
            haiku = ''
            possible_haiku = ''
            for word in split_tweet:
                lower_word = word.lower()
                for c in lower_word:
                    if not c.isalpha():
                        lower_word = lower_word.replace(c, '')
                if lower_word in self.syllables_dict:
                    syllables_counter += self.syllables_dict[lower_word]
                else:
                    syllables_counter = -999
                possible_haiku += word + ' '
                if syllables_counter == 5 and stage_counter == 0:
                    stage_counter += 1
                    possible_haiku += '\\' + '\n'
                if syllables_counter == 12 and stage_counter == 1:
                    possible_haiku += '\\' + '\n'
                    stage_counter += 1
                if syllables_counter == 17 and stage_counter == 2:
                    stage_counter += 1
                    possible_haiku += '\\' + '\n' + '@' + username
                    haiku = possible_haiku
                if syllables_counter != 17:
                    haiku = ''
            if haiku != '':
                haiku_list.append(haiku)
        with open('haiku.pickle', 'wb') as f:
            pickle.dump(haiku_list, f)

    def generate_haiku_by_word(self):
        """Creates a dict with words and the haiku's in which these words
        occur.
        """

        haiku_by_word = defaultdict(list)
        word_set = set([word for haiku in self.haiku_list for word in tokenization.tokenize(haiku)[:-1]])
        for word in word_set:
            for haiku in self.haiku_list:
                if word in tokenization.tokenize(haiku):
                    haiku_by_word[word].append(haiku)
        with open('haiku_by_word.pickle', 'wb') as f:
            pickle.dump(haiku_by_word, f)

    def generate_hashtag_haiku_list(self):
        """Creates a list of haiku's that contain hashtags."""

        hashtag_haiku_list = []
        for haiku in self.haiku_list:
            for c in haiku:
                if c == '#' and haiku not in hashtag_haiku_list:
                    hashtag_haiku_list.append(haiku)
        self.hashtag_haiku_list = hashtag_haiku_list

    def get_haiku(self):
        """Returns a random haiku."""

        rng = random.randrange(len(self.haiku_list))
        haiku = self.haiku_list[rng]
        return haiku

    def get_pop_hashtags(self):
        """Creates a list of the three most popular hashtags."""

        hashtag_list = []
        for haiku in self.hashtag_haiku_list:
            split_haiku = haiku.split()
            for word in split_haiku:
                if '#' in word:
                    hashtag_list.append(word)
        most_freq_hashtags = Counter(hashtag_list).most_common()[:3]
        self.most_freq_hashtags = [tag for tag, freq in most_freq_hashtags]

    def get_pop_haiku(self):
        """Returns a random haiku that contains one of the three most
        popular hashtags.
        """

        pop_haiku_list = []
        for haiku in self.hashtag_haiku_list:
            split_haiku = haiku.split()
            for word in split_haiku:
                if word in self.most_freq_hashtags:
                    pop_haiku_list.append(haiku)
        rng = random.randrange(len(pop_haiku_list))
        haiku = pop_haiku_list[rng]
        return haiku

    def search_haiku(self, search_word):
        """Returns a random haiku that contains search_word."""

        try:
            haiku_list = self.haiku_by_word[search_word]
            rng = random.randrange(len(haiku_list))
            haiku = haiku_list[rng]
            return haiku
        except ValueError:
            return "No matching haiku's were found"
