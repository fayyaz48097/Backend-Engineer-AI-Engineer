user = {
    "name": "Fayyaz",
    "age": 24,
    "city": "Lahore"
}

print(user["name"])
print(user["age"])
print(user["city"])

user["profession"] = "Software Engineer"  # add new values

user["name"] = "derek white" #update value 
print(user)

print(type(user))

# removing data in object

city = user.pop("city")
print(user)

print(city)

del user["age"]

print(user)

print(user.get("email"))


print(user.get("email", "No email provided"))


user = {
    "name": "Fayyaz",
    "age": 24,
    "city": "Lahore"
}



print(user.keys())

print(user.values())
print("==========================")
print(user.items())
print("==========================")



# Loops

user = {
    "name": "Fayyaz",
    "age": 24,
    "city": "Lahore"
}

for key, value in user.items():
    print(key, value)

#  nested

user = {
    "name": "Fayyaz",
    "age": 24,
    "address": {
        "city": "Lahore",
        "country": "Pakistan"
    }
}


print(user["address"])
print(user["address"]["city"])

user = {
    "name": "Fayyaz",
    "skills": ["Laravel", "JavaScript", "Python"]
}

print(user["skills"][0])

response = {
    "model": "some-model",
    "usage": {
        "input_tokens": 100,
        "output_tokens": 250
    },
    "message": {
        "role": "assistant",
        "content": "Hello Fayyaz!"
    }
}