# file = open("users.txt", "w")

# file.write("Fayyaz")
# file.write("ali")

# file.close()

# file = open("users.txt", "a")
# file.write("John")
# file.close()

# file = open("users.txt", "r")

# content = file.read()

# print(content)

# file.close()

# file = open("users.txt", "a")
# file = open("users.txt", "w")

# file.write("Fayyaz\n")
# file.write("Ali\n")
# file.write("Python Developer\n")

# file.close()

# file = open("users.txt", "r")

# lines = file.readline()
# lines = file.readline()
# lines = file.readline()

# print(lines +" line 1")
# file.close()
# file = open("users.txt", "r")

# print(file.readline().strip())
# print(file.readline().strip())
# print(file.readline().strip())

# file.close()

# file = open("users.txt", "r")

# print(file.tell())

# print(file.readline())

# print(file.tell())

# print(file.readline())

# print(file.tell())

# print(file.readline())

# print(file.tell())

# print(file.readline())

# file.close()

# file = open("users.txt", "r")

# print(file.readline())

# file.seek(8)

# print(file.readline())

# file.close()


with open("users.txt", "r") as file:
    content = file.read()
    print(content)


try:
    with open("does_not_exist.txt", "r") as file:
        content = file.read()

except FileNotFoundError:
    print("File does not exist.")


with open("users.txt", "r") as file:
    for line in file:
        print(line.strip())