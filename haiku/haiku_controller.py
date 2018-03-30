from haiku_view import *
from haiku_model import *


class HaikuController:

    def __init__(self):
        self.haiku_model = HaikuModel()
        self.run()

    def run(self):
        HaikuView(self.haiku_model)

HaikuController()
