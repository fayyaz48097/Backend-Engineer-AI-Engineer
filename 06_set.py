numbers = {10, 20, 30, 20, 10}

print(numbers)

skills = set(["Python", "Laravel", "Python"])

print(skills)

numbers = [1, 2, 2, 3, 3, 3, 4]

unique_numbers = set(numbers)

print(unique_numbers)

skills = {"Python", "Laravel"}

skills.add("AI")

print(skills)

skills = {"Python",  "AI"}

# skills.remove("Laravel")
skills.discard("Laravel")
print(skills)


# Membership

skills = {"Python", "Laravel", "AI"}

print("Python" in skills)
print("Java" in skills)


allowed_roles = {"admin", "manager"}

role = "developer"

if role in allowed_roles:
    print("Access allowed")
else: print("not access allowed")

# Set operations

backend = {"PHP", "Python", "Laravel"}
ai = {"Python", "TensorFlow", "PyTorch"}

print(backend | ai) # union

# Intersection

print(backend & ai)


# Difference

print(backend - ai)
print(ai - backend  )



numbers = {10, 20, 30, 20, 10}

print(numbers)
print(20 in numbers)
print(100 in numbers)

numbers.add(40)
print(numbers)

numbers.remove(10)
print(numbers)


print("===================================")


a = {1, 2, 3, 4}
b = {3, 4, 5, 6}

print(a | b) # union
print(a & b) # intersection
print(a - b) # diffrence
print(b - a)