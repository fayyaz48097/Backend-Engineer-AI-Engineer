# we'll learn Functions, including parameters, return values, default arguments, scope, and then *args / **kwargs. These are particularly important before we start modules, APIs, and the AI API client.


def say_hello():
    print("Hello Fayyaz")


# say_hello("fayyaz")  error can't call before defination
say_hello()

def say_hello(name):
    print(f"Hello {name}")

say_hello("fayyaz")

def add(a, b):
    print(a + b)


add(5, 10)


# 4. return

def add(a, b):
    return a + b

result = add(25,51)

print(result)


def get_user():
    return {
        "name": "Fayyaz",
        "age": 24
    }

user = get_user()

print(user["name"])

def get_user():
    return "Fayyaz", 24, "Lahore"

name , age , city = get_user()

print(name)
print(age)
print(city)

def greet(name="Fayyaz"):
    print(f"Hello {name}")

greet()

greet("ali")


def introduce(name, age):
    print(f"My name is {name} and I am {age}")

introduce(age=24, name="Fayyaz")



# Scope


name = "Fayyaz"

def test():
    name = "Ali"
    print(name) # Local

test()

print(name)  # Global scope


# 10. *args ⭐

def add(*numbers):
    print(numbers)

add(10,50)


def add(*numbers):
    total = 0
    for number in numbers:
        total = total + number #  total += number
    return total
        

result = add(10,50)

print(result)
print(add(10, 20, 30, 40))

# 11. **kwargs ⭐

# **kwargs collects keyword arguments into a dictionary.

def show_user(**data):
    print(data)

show_user(name="Fayyaz",
    age=24,
    city="Lahore")    

# So remember:

# *args    → tuple
# **kwargs → dictionary

# This is a very important Python concept.

# 12. *args + **kwargs

def test(*args, **kwargs):
    print(args)
    print(kwargs)

test(
    10,
    20,
    name="Fayyaz",
    age=24
)   



def greet(name):
    print(f"Hello {name}")

greet("Fayyaz")


def calculate(a, b):
    return a+ b

result = calculate(10, 20)

print(result)


def create_user(name, age, city):
    return{
        "name" : name,
        "age" : age,
        "city" : city
    }

user = create_user("Fayyaz" , 24, "Lahore")

print(user)