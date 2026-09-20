# Part 2 — Loops 🔄

# A loop means:

# Repeat something.


names = ["Ali", "Ahmed", "Fayyaz"]

for name in names:
    print(name)

# 7. Loop through a dictionary

user = {
    "name": "Fayyaz",
    "age": 24,
    "city": "Lahore"
}

for key, value in user.items():
    print(key, value)


for key in user:
    print(key)

for value in user.values():
 print(value)


#  8. range()

for i in range(5):
    print(i)
print("==========================")

for i in range(2, 6):
    print(i)

print("==========================")

for i in range(0, 10, 2):
    print(i)


print("==========WHILE LOOP================")

count = 1

while count <= 5:
    print(count)
    count += 1

while False:
    print("Hello")

print("========== Break ================")

for number in range(10):
    if number == 5:
        break

    print(number)

print("========== continue ================")

for number in range(5):
    if number == 2:
        continue

    print(number)

# Difference:

# break     → stop the entire loop
# continue  → skip this iteration

# 12. Conditions + loops together 🔥

print("========== loop and cond ================")

numbers = [10, 15, 20, 25, 30]

for number in numbers:
    if number <= 20:
        print(number)


users = [
    ["Ali", 24],
    ["Ahmed", 27],
    ["Fayyaz", 24]
]

for user in users:
    print(user)
    for value in user:
        print(value)


numbers = [10, 20, 30, 40, 50]

for number in numbers:
    if number >= 30:
        print(number)

print("========== exerscie ================")



users = [
    {"name": "Ali", "age": 17},
    {"name": "Ahmed", "age": 22},
    {"name": "Fayyaz", "age": 24}
]


for user in users:
    if user['age'] >=18:
        print(user["name"])

#  Challenge


students = [
    {"name": "Ali", "score": 85},
    {"name": "Ahmed", "score": 62},
    {"name": "Fayyaz", "score": 92},
    {"name": "Usman", "score": 45}
]

# Write a loop that prints:

# Ali passed
# Ahmed passed
# Fayyaz passed
# Usman failed

# Rule: score >= 50 → passed, otherwise → failed.

for student in students:
    if student["score"] >=50:
        print(f"{student['name']} passed")
    else:
       print(student["name"] + " " + "failed")  
