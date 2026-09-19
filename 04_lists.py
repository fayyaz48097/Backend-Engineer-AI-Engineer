# So we're going to learn lists properly, including:

# What a list actually is
# Creating lists
# Indexing
# Negative indexing
# Slicing
# Changing elements
# append()
# insert()
# extend()
# remove()
# pop()
# sort()
# reverse()
# len()
# in
# Nested lists
# List copying
# Mutable vs immutable objects
# What happens behind the scenes


mixed = [
    "Fayyaz",
    24,
    True,
    10.5,
    None
]


print(type(mixed))


print(mixed[2])

# list = mixed.split()

# print(list)


names = ["Ali", "Ahmed"]

names.append(True)

print(names)


names = ["Ali", "Ahmed", "Fayyaz"]

names.insert(1, "Usman")

print(names)

# insert(index, value)

# extend()


name = ["AI-Engineer","Fayyaz"]

more_name = ["Ali", "alhamdulillah"]


print(name.extend(more_name)) # didn't print any thing, but next like its modify name.

print(name)


print(name.append(more_name)) # didn't print any thing, but next like its modify name.

name = ["AI-Engineer","Fayyaz"]

more_name = ["Ali", "alhamdulillah"]

name.append(more_name)

print(name)




name.remove("AI-Engineer")

print(name)


name.pop(0)

print(name)


# name.pop([0][0])   i want to remove the 

# print(name)

# pop()

names = ["Ali", "Ahmed", "Fayyaz"]

names.pop()


print(names)

names.pop(0)


print(names)

names = ["Ali", "Ahmed", "Fayyaz"]

print(len(names))

# names = "fayyaz"
print(names[::-1])  # just test


numbers = [10, 20, 30, 40, 50]

print(numbers[1:4])
print(numbers[::-1])

numbers[0] = 100
print(numbers)

numbers.append(60)
print(numbers)

a = [1, 2, 3]
b = [4, 5]

a.append(b)

print(a)

# sort()

numbers = [50, 10, 40, 20, 30]

numbers.sort()

print(numbers)

numbers.sort(reverse=True)

print(numbers)


# reverse]

numbers = [10, 30, 20, 40]

numbers.reverse()

print(numbers)

print(11  not in numbers)


users = [
    ["Ali", 24],
    ["Ahmed", 27],
    ["Fayyaz", 24]
]


print(users[0])
print(users[0][0])


# users.

a = [1, 2, 3]

b = a

b.append(4)

print(a)
print(b)


a = [1, 2, 3]

b = a.copy()

b.append(4)

print(a)
print(b)


data = [10, 20, 30]

x = data

x[0] = 100

print(data)
print(x)


users = [
    ["Ali", 24],
    ["Ahmed", 27],
    ["Fayyaz", 24]
]

users[0].append("Developer")
users[1].remove(27)
users.append(["Usman", 30])

users[3].append(["hamza", 30])

print(users)