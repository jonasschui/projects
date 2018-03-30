def tokenize(string):
    '''Tokenizes a string'''

    lower_string = string.lower()
    for c in lower_string:
        if not c.isalpha():
            lower_string = lower_string.replace(c, ' ') 
    return lower_string.split()
