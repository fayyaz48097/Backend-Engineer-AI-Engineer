# import json

# user = {
#     "name": "Fayyaz",
#     "age":25,
#     "developer": True
# }

# json_data = json.dumps(user)

# print(json_data)
# print(type(json_data))

# json_data = '{"name": "Fayyaz", "age": 24, "developer":true}'

# user = json.loads(json_data)

# print(user)
# print(type(user))


import json

# user = {
#     "name": "Fayyaz",
#     "age": 25,
#     "developer": True,
#     "skills": ["Python", "Laravel", "AI"]
# }

# with open("users.json", "w") as file:
#     json.dump(user, file, indent=4)



# profile = {
#     "name": "fayyaz",
#     "Active": True,
#     "Developer": True,
#     "age": 25
# }

# with open("users.json","a") as file:
#     json.dump(profile, file, indent=4)


# import json

# user = {
#     "name": "Fayyaz",
#     "age": 25,
#     "developer": True,
#     "skills": ["Python", "Laravel", "AI"]
# }

# profile = {
#     "active": True,
#     "developer": True,
#     "age": 25
# }

# user.update(profile)

# with open("users.json", "w") as file:
#     json.dump(user, file, indent=4)


# import json

# user = {
#     "name": "Fayyaz",
#     "age": 25,
#     "developer": True,
#     "skills": ["Python", "Laravel", "AI"]
# }

# profile = {
#     "active": True,
#     "developer": True,
#     "age": 25
# }

# user["profile"] = profile

# with open("users.json", "w") as file:
#     json.dump(user, file, indent=4)



# # read json
# with open("users.json", "r") as file:
#     user = json.load(file)

# print(user)
# print(type(user))

# user["age"] = 26
# user["skills"] = ["Python", "AI", "Laravel"]
# with open("users.json", "w") as file:
#     json.dump(user, file, indent=4)


user = {
    "name": "Fayyaz",
    "age": 25,
    "developer": True
}

with open("users.json", "w") as file:
      file = json.dump(user, file, indent=4)


with open("users.json", "r") as file:
      user = json.load(file)
      print(user)


user["country"] = "Pakistan"
user["skills"]=["Python", "AI"]


with open("users.json", "w") as file:
      file = json.dump(user, file, indent=4)


with open("users.json", "r") as file:
      user = json.load(file)
      print(user)
