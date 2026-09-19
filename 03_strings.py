name = "Fayyaz"
city = 'Lahore'
message = """This is
a multi-line
string."""


print(name[0], name[1], name[2], name[3], name[4], name[5])
print(name[-1])
print(name[-5])
print(name[-6])  
#  positive start with 0 but negitive start with -1 means last is negative of total string length

# length of string

print(len(name))

# slice

print(name[0:3]) # Fay  sting[start:end]

print(name[3:6])  # start inclusive but end is exclusive

# Step in Slicing
# string[start:end:step]

print(name[::3])

print(name[::-1])
print(name[::-2])

# Python strings are immutable.

name = "Fayyaz"

name = "X" + name[1:3]

print(name)


name = "Fayyaz"

name ="ali"

print(name)


first_name = "Fayyaz"
last_name = "Ali"

full_name = first_name + " " + last_name

print(full_name)

age = 24

print("Age: " + str(age))

print(f"Age: {age}")



text = "  Hello World  "

print(text.strip())
print(text.replace("World", "Python"))

# split()

sentence = "I am learning AI Engineering"

words = sentence.split()

print(words) # convert it into list in simple word array


data = "apple,banana,mango"

print(data.split(","))

# join()

words = ["I", "am", "learning", "Python"]

sentence = "_".join(words)

print(sentence)

# So:

# split()
# String → List

# and:

# join()
# List → String

print(name.upper()+ " " +name.lower())
print(name.lower())
print(name.capitalize())


name = "Fayyaz"

# print(name[-7]) # error out of index range

print("Fayyaz" * 3)

print("Fayyaz" + " " + "Ali")
sentence = "Python is powerful"

print(sentence.split(" "))



# One thing I want you to notice:

# text = "  Hello World  "

# print(text.strip())
# print(text)

# The second print() will still have the spaces.

# Why?

# Because strings are immutable.

# Methods such as:

# strip()
# replace()
# upper()
# lower()

# return a new string rather than modifying the existing string.


text = "  Python AI Engineering  "
print(len(text))

clean = text.strip()

print(clean)
print(len(clean))

print(clean.upper())
print(clean.lower())


print(clean[0])
print(clean[-1])


print(clean[0:8])

words = clean.split()

print(words)
print(len(words))  # as in list then consider only the words not letter

result = "-".join(words)

print(result) # by join list convert to the string

print(type(result)) # proved


print("Python"[1:4])

text = "I am learning Python"

print("Python" in text)
print("Java" in text)
print("Java" not in text)


text = "Python AI"

print("Python" in text)
print("python" in text) # different for python
print("Java" not in text)
print(text[0:6])
print(text[::-1])