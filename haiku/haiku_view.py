from tkinter import *
from Haiku_bot import *


class HaikuView:

    def __init__(self, haiku_model):

        self.twitter = TwitterAPI()
        self.haiku_model = haiku_model
        self.haiku = ""
        self.theme = ""
        self.run_GUI()

    def search_keyword(self):
        """ Searches for keyword in given Tweets """
        self.keyword = self.default_entry.get()
        self.haiku = self.haiku_model.search_haiku(self.keyword)
        self.hashtags = " #OnbedoeldeHaiku"
        self.var.set("\n" + self.haiku + self.hashtags + "\n")

    def add_hashtag(self):
        """ Adds hashtag to tweet """
        self.hashtag = " " + self.default_entry.get()
        self.hashtags = " #OnbedoeldeHaiku" + self.hashtag
        self.var.set("\n" + self.haiku + self.hashtags + "\n")

    def generate_haiku(self):
        """Displays a random haiku."""

        self.haiku = self.haiku_model.get_haiku()
        self.hashtags = " #OnbedoeldeHaiku"
        self.var.set("\n" + self.haiku + self.hashtags + "\n")

    def generate_pop_haiku(self):
        """Displays a random popular haiku."""

        self.haiku = self.haiku_model.get_pop_haiku()
        self.hashtags = " #OnbedoeldeHaiku"
        self.var.set("\n" + self.haiku + self.hashtags + "\n")

    def tweet_haiku(self):
        twitter = TwitterAPI()
        twitter.tweet(self.haiku + self.hashtags)

    def close_GUI(self):
        """Closes the window."""

        self.window.destroy()

    def run_GUI(self):
        """Creates a window including a fixed size"""

        # creates a window including a fixed size
        self.window = Tk()
        self.window.geometry("900x550+200+200")
        self.window.title("Haiku")
        self.window.configure(background="white")
        self.window.resizable(width=FALSE, height=FALSE)

        # draws logo in interface
        photo = PhotoImage(file="logo.gif")
        label = Label(self.window, image=photo).pack()

        # draws haiku
        self.var = StringVar()
        show = Label(self.window, textvariable=self.var).pack()
        self.var.set("Click search to find tweet containing searchword \n "
                "Click generate for a random haiku \n "
                "Click generate popular haiku for a random popular haiku \n "
                "Enter a hashtag and click add hashtag to add to tweet \n"
                "Click tweet when you find a suitable haiku \n "
                "Click close window to exit")

        # draws entry widget
        self.default_entry = StringVar()
        search_entry = Entry(self.window, width=30, textvariable=self.default_entry).pack()

        # draws five buttons
        search_button = Button(self.window, text="search keyword", width=20, command=self.search_keyword).pack()
        generate_button = Button(self.window, text="generate", width=20, command=self.generate_haiku).pack()
        generate_pop_button = Button(self.window, text="generate popular haiku", width=20, command=self.generate_pop_haiku).pack()
        add_hashtag_button = Button(self.window, text="add hashtag", width=20, command=self.add_hashtag).pack()
        tweet_button = Button(self.window, text="Tweet", width=20, command=self.tweet_haiku).pack()
        close_button = Button(self.window, text="close window", width=20, command=self.close_GUI).pack()

        # makes it possible for the window to run
        self.window.mainloop()
